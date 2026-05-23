<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Saas_gateway_base.php';

/**
 * Nagad PGW (Payment Gateway) v1 adapter.
 *
 * Credentials hash keys:
 *   merchant_id, merchant_number, public_key, private_key
 *   is_sandbox=true  → http://sandbox.mynagad.com:10080
 *   is_sandbox=false → https://api.mynagad.com
 *
 * Nagad's PGW signs every request with the merchant's RSA private key and
 * encrypts sensitive fields with Nagad's public key. This adapter delegates
 * crypto to PHP's openssl extension; if openssl is unavailable the adapter
 * declares itself "not configured" so it gets filtered out of the pay page.
 *
 * High-level flow (PGW v1):
 *   1. POST /check-out/initialize/{merchant_id}/{order_id}  →  paymentReferenceId, challenge
 *   2. POST /check-out/complete/{paymentReferenceId}        →  callBackUrl
 *   3. user finishes on Nagad                               →  redirect-back with payment_ref_id, status
 *   4. GET  /verify/payment/{payment_ref_id}                →  final status
 */
class Saas_nagad_gateway extends Saas_gateway_base
{
    protected $code = 'nagad';
    protected $name = 'Nagad';
    protected $supports_recurring = false;
    protected $required_credentials = ['merchant_id', 'merchant_number', 'public_key', 'private_key'];

    public function is_configured()
    {
        if (!extension_loaded('openssl')) return false;
        return parent::is_configured();
    }

    protected function base_url()
    {
        return $this->is_sandbox
            ? 'http://sandbox.mynagad.com:10080/api/dfs'
            : 'https://api.mynagad.com/api/dfs';
    }

    public function initiate($invoice, $branch)
    {
        if (!$this->is_configured()) {
            return ['action' => 'error', 'message' => 'Nagad is not configured (missing keys or openssl extension).'];
        }

        $orderId = $invoice->invoice_no;
        $datetime = date('YmdHis');

        // Step 1 — initialize.
        $sensitive = json_encode([
            'merchantId'    => $this->credentials['merchant_id'],
            'datetime'      => $datetime,
            'orderId'       => $orderId,
            'challenge'     => $this->generate_challenge(),
        ], JSON_UNESCAPED_SLASHES);

        $payload = [
            'accountNumber' => $this->credentials['merchant_number'],
            'dateTime'      => $datetime,
            'sensitiveData' => $this->encrypt_with_public_key($sensitive),
            'signature'     => $this->sign_with_private_key($sensitive),
        ];

        list($code, $body, $err) = $this->http_post(
            $this->base_url() . '/check-out/initialize/' . rawurlencode($this->credentials['merchant_id']) . '/' . rawurlencode($orderId),
            $payload,
            ['X-KM-IP-V4: ' . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 'X-KM-Client-Type: PC_WEB'],
            /* asJson */ true
        );
        if ($err || $code !== 200) {
            return ['action' => 'error', 'message' => 'Nagad initialize HTTP error: ' . ($err ?: $code)];
        }
        $resp = json_decode($body, true);
        if (!is_array($resp) || empty($resp['sensitiveData'])) {
            return ['action' => 'error', 'message' => 'Nagad initialize failed: ' . substr((string)$body, 0, 200)];
        }
        $decrypted = $this->decrypt_with_private_key($resp['sensitiveData']);
        $init = json_decode($decrypted, true);
        if (!is_array($init) || empty($init['paymentReferenceId'])) {
            return ['action' => 'error', 'message' => 'Nagad initialize: missing paymentReferenceId.'];
        }

        // Step 2 — complete.
        $completeSensitive = json_encode([
            'merchantId'    => $this->credentials['merchant_id'],
            'orderId'       => $orderId,
            'currencyCode'  => '050',  // BDT ISO-4217 numeric
            'amount'        => number_format((float)$invoice->amount, 2, '.', ''),
            'challenge'     => $init['challenge'] ?? '',
        ], JSON_UNESCAPED_SLASHES);

        $completePayload = [
            'sensitiveData' => $this->encrypt_with_public_key($completeSensitive),
            'signature'     => $this->sign_with_private_key($completeSensitive),
            'merchantCallbackURL' => $this->callback_url('success') . '?invoice_id=' . (int)$invoice->id,
        ];

        list($code2, $body2, $err2) = $this->http_post(
            $this->base_url() . '/check-out/complete/' . rawurlencode($init['paymentReferenceId']),
            $completePayload,
            ['X-KM-IP-V4: ' . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'), 'X-KM-Client-Type: PC_WEB'],
            /* asJson */ true
        );
        if ($err2 || $code2 !== 200) {
            return ['action' => 'error', 'message' => 'Nagad complete HTTP error: ' . ($err2 ?: $code2)];
        }
        $resp2 = json_decode($body2, true);
        if (!is_array($resp2) || empty($resp2['callBackUrl'])) {
            return ['action' => 'error', 'message' => 'Nagad complete failed: ' . substr((string)$body2, 0, 200)];
        }
        return ['action' => 'redirect', 'url' => $resp2['callBackUrl']];
    }

    public function handle_callback($invoiceId, array $payload)
    {
        $ref    = $payload['payment_ref_id'] ?? null;
        $status = strtolower((string)($payload['status'] ?? ''));
        if (!$ref || $status === 'cancel')      return ['status' => 'cancelled', 'txn_id' => $ref,  'raw' => $payload];
        if ($status !== 'success')              return ['status' => 'failed',    'txn_id' => $ref,  'raw' => $payload];

        list($code, $body, $err) = $this->http_get(
            $this->base_url() . '/verify/payment/' . rawurlencode($ref),
            ['X-KM-Client-Type: PC_WEB']
        );
        if ($err || $code !== 200) {
            return ['status' => 'pending', 'txn_id' => $ref, 'raw' => ['err' => $err, 'http' => $code, 'body' => $body]];
        }
        $resp = json_decode($body, true);
        if (is_array($resp) && strtolower($resp['status'] ?? '') === 'success') {
            return ['status' => 'succeeded', 'txn_id' => $resp['issuerPaymentRefNo'] ?? $ref, 'raw' => $resp];
        }
        return ['status' => 'failed', 'txn_id' => $ref, 'raw' => $resp];
    }

    public function handle_ipn($invoiceId, array $payload)
    {
        return $this->handle_callback($invoiceId, $payload);
    }

    // -------------------------------------------------------------------------
    // Crypto helpers
    // -------------------------------------------------------------------------

    protected function generate_challenge()
    {
        return bin2hex(random_bytes(20));
    }

    protected function pem_wrap($key, $type)
    {
        // Accept either raw base64 or already-wrapped PEM.
        if (strpos($key, 'BEGIN') !== false) return $key;
        $chunked = chunk_split($key, 64, "\n");
        return "-----BEGIN {$type}-----\n{$chunked}-----END {$type}-----\n";
    }

    protected function encrypt_with_public_key($plain)
    {
        $public = openssl_pkey_get_public($this->pem_wrap($this->credentials['public_key'], 'PUBLIC KEY'));
        if (!$public) return null;
        openssl_public_encrypt($plain, $crypted, $public, OPENSSL_PKCS1_PADDING);
        return base64_encode($crypted);
    }

    protected function decrypt_with_private_key($base64)
    {
        $private = openssl_pkey_get_private($this->pem_wrap($this->credentials['private_key'], 'RSA PRIVATE KEY'));
        if (!$private) return null;
        openssl_private_decrypt(base64_decode($base64), $decrypted, $private, OPENSSL_PKCS1_PADDING);
        return $decrypted;
    }

    protected function sign_with_private_key($plain)
    {
        $private = openssl_pkey_get_private($this->pem_wrap($this->credentials['private_key'], 'RSA PRIVATE KEY'));
        if (!$private) return null;
        openssl_sign($plain, $signature, $private, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }
}

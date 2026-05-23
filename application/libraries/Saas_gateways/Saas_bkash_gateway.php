<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Saas_gateway_base.php';

/**
 * bKash Tokenized Checkout (PGW) adapter.
 *
 * Credentials hash keys:
 *   app_key, app_secret, username, password
 *   is_sandbox=true  → https://tokenized.sandbox.bka.sh/v1.2.0-beta
 *   is_sandbox=false → https://tokenized.pay.bka.sh/v1.2.0-beta
 *
 * Flow:
 *   1. POST /token/grant         → id_token
 *   2. POST /checkout/create     → paymentID + bkashURL  → redirect user
 *   3. user finishes on bKash    → redirect-back with paymentID + status=success|cancel|failure
 *   4. POST /checkout/execute    → final status (executed/failed)
 *   5. (server-side) POST /checkout/payment/status → idempotent re-check
 *
 * Tokens are short-lived (≈1 hour); we re-fetch one for every server-to-server
 * call rather than caching across requests.
 */
class Saas_bkash_gateway extends Saas_gateway_base
{
    protected $code = 'bkash';
    protected $name = 'bKash';
    protected $supports_recurring = false;
    protected $required_credentials = ['app_key', 'app_secret', 'username', 'password'];

    protected function base_url()
    {
        return $this->is_sandbox
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    }

    protected function grant_token()
    {
        list($code, $body, $err) = $this->http_post(
            $this->base_url() . '/tokenized/checkout/token/grant',
            [
                'app_key'    => $this->credentials['app_key'],
                'app_secret' => $this->credentials['app_secret'],
            ],
            [
                'username: ' . $this->credentials['username'],
                'password: ' . $this->credentials['password'],
            ],
            /* asJson */ true
        );
        if ($err || $code !== 200) return null;
        $resp = json_decode($body, true);
        return is_array($resp) && !empty($resp['id_token']) ? $resp['id_token'] : null;
    }

    protected function auth_headers($token)
    {
        return [
            'Authorization: ' . $token,
            'X-APP-Key: '     . $this->credentials['app_key'],
        ];
    }

    public function initiate($invoice, $branch)
    {
        if (!$this->is_configured()) {
            return ['action' => 'error', 'message' => 'bKash is not configured.'];
        }
        $token = $this->grant_token();
        if (!$token) return ['action' => 'error', 'message' => 'bKash token grant failed.'];

        list($code, $body, $err) = $this->http_post(
            $this->base_url() . '/tokenized/checkout/create',
            [
                'mode'                  => '0011',     // checkout
                'payerReference'        => (string)$invoice->branch_id,
                'callbackURL'           => $this->callback_url('success') . '?invoice_id=' . (int)$invoice->id,
                'amount'                => number_format((float)$invoice->amount, 2, '.', ''),
                'currency'              => $invoice->currency ?: 'BDT',
                'intent'                => 'sale',
                'merchantInvoiceNumber' => $invoice->invoice_no,
            ],
            $this->auth_headers($token),
            /* asJson */ true
        );
        if ($err || $code !== 200) {
            return ['action' => 'error', 'message' => 'bKash create payment HTTP error: ' . ($err ?: $code)];
        }
        $resp = json_decode($body, true);
        if (!is_array($resp) || empty($resp['bkashURL']) || empty($resp['paymentID'])) {
            return ['action' => 'error', 'message' => 'bKash create payment failed: ' . substr((string)$body, 0, 200)];
        }
        return ['action' => 'redirect', 'url' => $resp['bkashURL']];
    }

    public function handle_callback($invoiceId, array $payload)
    {
        $paymentId = $payload['paymentID'] ?? null;
        $status    = strtolower((string)($payload['status'] ?? ''));
        if (!$paymentId || $status === 'cancel')   return ['status' => 'cancelled', 'txn_id' => $paymentId, 'raw' => $payload];
        if ($status === 'failure')                 return ['status' => 'failed',    'txn_id' => $paymentId, 'raw' => $payload];

        $token = $this->grant_token();
        if (!$token) return ['status' => 'pending', 'txn_id' => $paymentId, 'raw' => $payload];

        list($code, $body, $err) = $this->http_post(
            $this->base_url() . '/tokenized/checkout/execute',
            ['paymentID' => $paymentId],
            $this->auth_headers($token),
            /* asJson */ true
        );
        if ($err || $code !== 200) {
            return ['status' => 'pending', 'txn_id' => $paymentId, 'raw' => ['err' => $err, 'http' => $code, 'body' => $body]];
        }
        $resp = json_decode($body, true);
        if (is_array($resp) && strtolower($resp['transactionStatus'] ?? '') === 'completed') {
            return ['status' => 'succeeded', 'txn_id' => $resp['trxID'] ?? $paymentId, 'raw' => $resp];
        }
        return ['status' => 'failed', 'txn_id' => $paymentId, 'raw' => $resp];
    }

    public function handle_ipn($invoiceId, array $payload)
    {
        // bKash doesn't push a server-to-server IPN for tokenized checkout
        // by default; the callback URL is the source of truth. We still
        // expose this method so the controller can re-query if needed.
        $paymentId = $payload['paymentID'] ?? null;
        if (!$paymentId) return ['status' => 'failed', 'txn_id' => null, 'raw' => $payload];
        $token = $this->grant_token();
        if (!$token) return ['status' => 'pending', 'txn_id' => $paymentId, 'raw' => $payload];

        list($code, $body, $err) = $this->http_post(
            $this->base_url() . '/tokenized/checkout/payment/status',
            ['paymentID' => $paymentId],
            $this->auth_headers($token),
            /* asJson */ true
        );
        if ($err || $code !== 200) {
            return ['status' => 'pending', 'txn_id' => $paymentId, 'raw' => ['err' => $err, 'http' => $code, 'body' => $body]];
        }
        $resp = json_decode($body, true);
        if (is_array($resp) && strtolower($resp['transactionStatus'] ?? '') === 'completed') {
            return ['status' => 'succeeded', 'txn_id' => $resp['trxID'] ?? $paymentId, 'raw' => $resp];
        }
        return ['status' => 'failed', 'txn_id' => $paymentId, 'raw' => $resp];
    }
}

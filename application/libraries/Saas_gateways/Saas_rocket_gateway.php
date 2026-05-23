<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Saas_gateway_base.php';

/**
 * Rocket (DBBL Mobile Banking) adapter — generic merchant API.
 *
 * DBBL Rocket does not expose a uniform public hosted-checkout API the
 * way bKash/Nagad do; merchants are typically given a per-account
 * endpoint + API key by DBBL. This adapter treats those as configurable
 * credentials and posts a checkout request, then redirects the user to
 * the response URL.
 *
 * Credentials hash keys:
 *   api_base    — full base URL DBBL provided (e.g. https://rocket-pgw.dbbl.com.bd)
 *   merchant_id, api_key, secret
 *
 * If DBBL hasn't given you a hosted-checkout integration yet, leave this
 * disabled; the manual / SSLCommerz path will cover Rocket via SSLCommerz's
 * aggregator emulator until a direct contract is in place.
 */
class Saas_rocket_gateway extends Saas_gateway_base
{
    protected $code = 'rocket';
    protected $name = 'Rocket (DBBL)';
    protected $supports_recurring = false;
    protected $required_credentials = ['api_base', 'merchant_id', 'api_key'];

    public function initiate($invoice, $branch)
    {
        if (!$this->is_configured()) {
            return ['action' => 'error', 'message' => 'Rocket is not configured.'];
        }
        $payload = [
            'merchant_id' => $this->credentials['merchant_id'],
            'amount'      => number_format((float)$invoice->amount, 2, '.', ''),
            'currency'    => $invoice->currency ?: 'BDT',
            'order_id'    => $invoice->invoice_no,
            'reference'   => 'SmartSchool-' . $invoice->id,
            'callback_url'=> $this->callback_url('success') . '?invoice_id=' . (int)$invoice->id,
            'cancel_url'  => $this->callback_url('cancel')  . '?invoice_id=' . (int)$invoice->id,
            'sandbox'     => $this->is_sandbox ? 1 : 0,
        ];
        $payload['signature'] = $this->sign($payload);

        list($code, $body, $err) = $this->http_post(
            rtrim($this->credentials['api_base'], '/') . '/checkout/create',
            $payload,
            ['X-API-KEY: ' . $this->credentials['api_key']],
            /* asJson */ true
        );
        if ($err || $code !== 200) {
            return ['action' => 'error', 'message' => 'Rocket HTTP error: ' . ($err ?: $code)];
        }
        $resp = json_decode($body, true);
        if (!is_array($resp) || empty($resp['redirect_url'])) {
            return ['action' => 'error', 'message' => 'Rocket init failed: ' . substr((string)$body, 0, 200)];
        }
        return ['action' => 'redirect', 'url' => $resp['redirect_url']];
    }

    public function handle_callback($invoiceId, array $payload)
    {
        $status = strtolower((string)($payload['status'] ?? ''));
        $txnId  = $payload['transaction_id'] ?? $payload['order_id'] ?? null;
        if ($status === 'success' || $status === 'completed') {
            // Optional: verify with the signature DBBL echoed back. If
            // the merchant has a verify endpoint, swap this for an HTTP
            // re-check; the conservative default is to trust the signed
            // redirect-back.
            if (isset($payload['signature']) && !$this->verify_signature($payload)) {
                return ['status' => 'failed', 'txn_id' => $txnId, 'raw' => $payload];
            }
            return ['status' => 'succeeded', 'txn_id' => $txnId, 'raw' => $payload];
        }
        if ($status === 'cancel' || $status === 'cancelled') {
            return ['status' => 'cancelled', 'txn_id' => $txnId, 'raw' => $payload];
        }
        return ['status' => 'failed', 'txn_id' => $txnId, 'raw' => $payload];
    }

    public function handle_ipn($invoiceId, array $payload)
    {
        return $this->handle_callback($invoiceId, $payload);
    }

    protected function sign(array $payload)
    {
        $secret = $this->credentials['secret'] ?? '';
        unset($payload['signature']);
        ksort($payload);
        return hash_hmac('sha256', http_build_query($payload), $secret);
    }

    protected function verify_signature(array $payload)
    {
        $given = $payload['signature'] ?? '';
        $payload = array_diff_key($payload, ['signature' => 1]);
        $expected = $this->sign($payload);
        return hash_equals($expected, $given);
    }
}

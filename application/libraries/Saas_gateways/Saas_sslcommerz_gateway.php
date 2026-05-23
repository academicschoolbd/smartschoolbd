<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Saas_gateway_base.php';

/**
 * SSLCommerz adapter for the SaaS billing pipeline.
 *
 * Credentials hash keys: store_id, store_passwd
 *   is_sandbox=true  → https://sandbox.sslcommerz.com/...
 *   is_sandbox=false → https://securepay.sslcommerz.com/...
 *
 * NOTE: this is intentionally NOT the same as application/libraries/Sslcommerz.php,
 * which reads from `payment_config` (per-school fee-collection settings) and is
 * used by /feespayment etc. The SaaS pipeline has its own credentials (one set
 * for the SaaS platform itself) and lives in `saas_payment_gateway`.
 */
class Saas_sslcommerz_gateway extends Saas_gateway_base
{
    protected $code = 'sslcommerz';
    protected $name = 'SSLCommerz';
    protected $supports_recurring = false;
    protected $required_credentials = ['store_id', 'store_passwd'];

    protected function submit_url()
    {
        $host = $this->is_sandbox ? 'sandbox.sslcommerz.com' : 'securepay.sslcommerz.com';
        return 'https://' . $host . '/gwprocess/v4/api.php';
    }

    protected function validation_url()
    {
        $host = $this->is_sandbox ? 'sandbox.sslcommerz.com' : 'securepay.sslcommerz.com';
        return 'https://' . $host . '/validator/api/validationserverAPI.php';
    }

    public function initiate($invoice, $branch)
    {
        if (!$this->is_configured()) {
            return ['action' => 'error', 'message' => 'SSLCommerz is not configured (missing store_id / store_passwd).'];
        }
        $post = [
            'store_id'     => $this->credentials['store_id'],
            'store_passwd' => $this->credentials['store_passwd'],
            'total_amount' => number_format((float)$invoice->amount, 2, '.', ''),
            'currency'     => $invoice->currency ?: 'BDT',
            'tran_id'      => $invoice->invoice_no,
            'success_url'  => $this->callback_url('success'),
            'fail_url'     => $this->callback_url('fail'),
            'cancel_url'   => $this->callback_url('cancel'),
            'ipn_url'      => $this->callback_url('ipn'),
            'cus_name'     => $branch->name,
            'cus_email'    => $branch->email ?: 'noreply@smartschool.bd',
            'cus_phone'    => $branch->mobileno ?: '01700000000',
            'cus_add1'     => $branch->address ?: 'N/A',
            'cus_city'     => $branch->city ?: 'Dhaka',
            'cus_country'  => 'Bangladesh',
            'shipping_method' => 'NO',
            'product_name' => 'SmartSchool SaaS Invoice ' . $invoice->invoice_no,
            'product_category' => 'subscription',
            'product_profile'  => 'non-physical-goods',
            'value_a'      => (string)$invoice->id, // invoice id round-trips back in callbacks
        ];
        list($code, $body, $err) = $this->http_post($this->submit_url(), $post);
        if ($err || $code !== 200) {
            return ['action' => 'error', 'message' => 'SSLCommerz HTTP error: ' . ($err ?: $code)];
        }
        $resp = json_decode($body, true);
        if (!is_array($resp) || ($resp['status'] ?? '') !== 'SUCCESS' || empty($resp['GatewayPageURL'])) {
            return ['action' => 'error', 'message' => 'SSLCommerz init failed: ' . ($resp['failedreason'] ?? 'unknown')];
        }
        return ['action' => 'redirect', 'url' => $resp['GatewayPageURL']];
    }

    public function handle_callback($invoiceId, array $payload)
    {
        $status = strtolower((string)($payload['status'] ?? 'failed'));
        $txnId  = $payload['tran_id'] ?? null;
        if ($status === 'valid' || $status === 'validated') {
            // Verify with validator API before trusting the redirect-back payload.
            if (!$this->verify_with_validator($payload)) {
                return ['status' => 'failed', 'txn_id' => $txnId, 'raw' => $payload];
            }
            return ['status' => 'succeeded', 'txn_id' => $payload['bank_tran_id'] ?? $txnId, 'raw' => $payload];
        }
        if ($status === 'cancelled') return ['status' => 'cancelled', 'txn_id' => $txnId, 'raw' => $payload];
        return ['status' => 'failed', 'txn_id' => $txnId, 'raw' => $payload];
    }

    public function handle_ipn($invoiceId, array $payload)
    {
        // Same verification path as the redirect callback.
        return $this->handle_callback($invoiceId, $payload);
    }

    protected function verify_with_validator(array $payload)
    {
        if (empty($payload['val_id'])) return false;
        $url = $this->validation_url()
             . '?val_id='     . rawurlencode($payload['val_id'])
             . '&store_id='   . rawurlencode($this->credentials['store_id'])
             . '&store_passwd=' . rawurlencode($this->credentials['store_passwd'])
             . '&format=json&v=1';
        list($code, $body, $err) = $this->http_get($url);
        if ($err || $code !== 200) return false;
        $resp = json_decode($body, true);
        if (!is_array($resp)) return false;
        $st = strtolower((string)($resp['status'] ?? ''));
        return $st === 'valid' || $st === 'validated';
    }
}

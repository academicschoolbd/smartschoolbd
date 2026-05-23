<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Saas_gateway_base.php';

/**
 * Paykure Ghor adapter — official spec at https://paykureghor.com/developers/docs.
 *
 * Endpoints
 * ---------
 *   Create  : POST https://checkout.paykureghor.com/api/payment/create
 *   Verify  : POST https://checkout.paykureghor.com/api/payment/verify
 *
 * Both calls authenticate via three custom headers (no HMAC signature
 * over the body). Sandbox vs. live is determined by which API keys you
 * paste — the URLs are the same in both environments per section 2 of
 * the docs.
 *
 * Headers
 *   API-KEY     — app key from Paykure Ghor API credentials
 *   SECRET-KEY  — secret key from Paykure Ghor API credentials
 *   BRAND-KEY   — brand key for the brand you're charging into
 *   Content-Type: application/json
 *
 * Credentials JSON keys
 *   api_key, secret_key, brand_key
 *
 * Create-payment request body
 *   {
 *     "cus_name":    "...",
 *     "cus_email":   "...",
 *     "amount":      "10" or "10.50",   (string, no trailing zeros required)
 *     "success_url": "https://your.site/success",
 *     "cancel_url":  "https://your.site/cancel",
 *     "metadata":    { ... free-form JSON ... }
 *   }
 *
 * Create-payment response
 *   { "status": "success", "payment_url": "https://..." }   on success
 *   The user is redirected to `payment_url`. When the charge completes
 *   Paykure Ghor redirects back to `success_url` with `?transaction_id=<id>`.
 *
 * Verify-payment request body
 *   { "transaction_id": "<from success_url>" }
 * Verify-payment response
 *   { "status": "COMPLETED", "transaction_id": "...", "amount": "...", ... }
 *
 * Paykure Ghor's section 2 does not document a separate IPN URL; the
 * `success_url` redirect is the source of truth. We still expose
 * /billing/ipn/paykureghor (handle_ipn() defaults to re-verifying) so
 * that future docs that do add IPN don't require a route change.
 */
class Saas_paykureghor_gateway extends Saas_gateway_base
{
    protected $code = 'paykureghor';
    protected $name = 'Paykure Ghor';
    protected $supports_recurring = false;
    protected $required_credentials = ['api_key', 'secret_key', 'brand_key'];

    const CREATE_URL = 'https://checkout.paykureghor.com/api/payment/create';
    const VERIFY_URL = 'https://checkout.paykureghor.com/api/payment/verify';

    public function initiate($invoice, $branch)
    {
        if (!$this->is_configured()) {
            return ['action' => 'error', 'message' => 'Paykure Ghor is not configured (missing api_key / secret_key / brand_key).'];
        }
        $body = [
            'cus_name'    => $branch->name ?: 'SmartSchool tenant',
            'cus_email'   => $branch->email ?: 'noreply@smartschool.bd',
            'amount'      => $this->_format_amount((float)$invoice->amount),
            // Round-trip the invoice id so /billing/success/paykureghor can locate it.
            'success_url' => $this->callback_url('success') . '?invoice_id=' . (int)$invoice->id,
            'cancel_url'  => $this->callback_url('cancel')  . '?invoice_id=' . (int)$invoice->id,
            'metadata'    => [
                'invoice_id'  => (int)$invoice->id,
                'invoice_no'  => $invoice->invoice_no,
                'branch_id'   => (int)$invoice->branch_id,
            ],
        ];
        list($code, $rawResp, $err) = $this->http_post(
            self::CREATE_URL,
            $body,
            $this->_auth_headers(),
            /* asJson */ true
        );
        if ($err || $code < 200 || $code >= 300) {
            return ['action' => 'error', 'message' => 'Paykure Ghor HTTP error: ' . ($err ?: $code) . ' ' . substr((string)$rawResp, 0, 200)];
        }
        $resp = json_decode($rawResp, true);
        if (!is_array($resp)) {
            return ['action' => 'error', 'message' => 'Paykure Ghor: unparseable response.'];
        }
        // The docs use `payment_url`; we also accept `redirect_url` and `url` in case the
        // field name shifts in a future docs revision.
        $redirect = $resp['payment_url'] ?? $resp['redirect_url'] ?? $resp['url'] ?? null;
        if (!$redirect) {
            return ['action' => 'error', 'message' => 'Paykure Ghor: missing payment_url in response.'];
        }
        return ['action' => 'redirect', 'url' => $redirect];
    }

    public function handle_callback($invoiceId, array $payload)
    {
        $txnId = $payload['transaction_id'] ?? null;
        if (!$txnId) {
            // Paykure Ghor only redirects to cancel_url on user cancel — there's no
            // transaction_id in that case. Treat the absence as cancelled.
            if ($this->_url_indicates_cancel()) {
                return ['status' => 'cancelled', 'txn_id' => null, 'raw' => $payload];
            }
            return ['status' => 'failed', 'txn_id' => null, 'raw' => $payload];
        }
        return $this->_verify($txnId, $payload);
    }

    public function handle_ipn($invoiceId, array $payload)
    {
        $txnId = $payload['transaction_id'] ?? null;
        if (!$txnId) return ['status' => 'failed', 'txn_id' => null, 'raw' => $payload];
        return $this->_verify($txnId, $payload);
    }

    // -------------------------------------------------------------------------
    // Internal
    // -------------------------------------------------------------------------

    protected function _verify($txnId, array $original)
    {
        list($code, $rawResp, $err) = $this->http_post(
            self::VERIFY_URL,
            ['transaction_id' => $txnId],
            $this->_auth_headers(),
            /* asJson */ true
        );
        if ($err || $code < 200 || $code >= 300) {
            return ['status' => 'pending', 'txn_id' => $txnId, 'raw' => ['err' => $err, 'http' => $code, 'body' => $rawResp]];
        }
        $resp = json_decode($rawResp, true);
        if (!is_array($resp)) return ['status' => 'pending', 'txn_id' => $txnId, 'raw' => $rawResp];

        $status = strtoupper((string)($resp['status'] ?? ''));
        // The docs sample shows COMPLETED; accept SUCCESS as a fallback.
        if ($status === 'COMPLETED' || $status === 'SUCCESS') {
            return [
                'status' => 'succeeded',
                'txn_id' => $resp['transaction_id'] ?? $txnId,
                'raw'    => $resp,
            ];
        }
        if ($status === 'PENDING') {
            return ['status' => 'pending', 'txn_id' => $txnId, 'raw' => $resp];
        }
        if ($status === 'CANCELLED' || $status === 'CANCELED') {
            return ['status' => 'cancelled', 'txn_id' => $txnId, 'raw' => $resp];
        }
        return ['status' => 'failed', 'txn_id' => $txnId, 'raw' => $resp];
    }

    protected function _auth_headers()
    {
        return [
            'API-KEY: '    . $this->credentials['api_key'],
            'SECRET-KEY: ' . $this->credentials['secret_key'],
            'BRAND-KEY: '  . $this->credentials['brand_key'],
        ];
    }

    /** Format BDT amount per Paykure Ghor: no trailing zeros for whole numbers. */
    protected function _format_amount($amount)
    {
        if (floor($amount) == $amount) return (string)(int)$amount;
        return rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.');
    }

    /** True when the current URL is /billing/cancel/paykureghor. */
    protected function _url_indicates_cancel()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return stripos($uri, '/billing/cancel/') !== false;
    }
}

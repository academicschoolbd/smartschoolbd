<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Saas_gateway_base.php';

/**
 * Stripe Checkout adapter for the SaaS billing pipeline.
 *
 * Credentials hash keys:
 *   secret_key      — sk_test_... / sk_live_...
 *   publishable_key — pk_test_... / pk_live_... (informational only)
 *   webhook_secret  — whsec_...  (verifies the IPN payload signature)
 *
 * Uses Stripe's Checkout Session API directly via HTTPS instead of the
 * vendored stripe-php SDK; this keeps the adapter self-contained and
 * avoids the risk of namespace collisions with the existing Ramom-era
 * fee-payment integrations in /application/third_party/stripe.
 */
class Saas_stripe_gateway extends Saas_gateway_base
{
    protected $code = 'stripe';
    protected $name = 'Stripe';
    protected $supports_recurring = true;
    protected $required_credentials = ['secret_key'];

    public function initiate($invoice, $branch)
    {
        if (!$this->is_configured()) {
            return ['action' => 'error', 'message' => 'Stripe is not configured (missing secret_key).'];
        }
        $currency = strtolower($invoice->currency ?: 'usd');
        // Stripe wants amounts in the smallest currency unit; BDT is zero-decimal
        // for some merchants but stripe treats it as 2-decimal — multiply by 100.
        $amount = (int)round(((float)$invoice->amount) * 100);

        $post = [
            'mode'            => 'payment',
            'success_url'     => $this->callback_url('success') . '?session_id={CHECKOUT_SESSION_ID}&invoice_id=' . (int)$invoice->id,
            'cancel_url'      => $this->callback_url('cancel')  . '?invoice_id=' . (int)$invoice->id,
            'client_reference_id' => $invoice->invoice_no,
            'metadata[invoice_id]'  => (string)$invoice->id,
            'metadata[invoice_no]'  => $invoice->invoice_no,
            'metadata[branch_id]'   => (string)$invoice->branch_id,
            'line_items[0][quantity]' => 1,
            'line_items[0][price_data][currency]'     => $currency,
            'line_items[0][price_data][unit_amount]'  => $amount,
            'line_items[0][price_data][product_data][name]'
                => 'SmartSchool SaaS Invoice ' . $invoice->invoice_no,
        ];

        list($code, $body, $err) = $this->http_post(
            'https://api.stripe.com/v1/checkout/sessions',
            $post,
            ['Authorization: Bearer ' . $this->credentials['secret_key']]
        );
        if ($err || $code < 200 || $code >= 300) {
            return ['action' => 'error', 'message' => 'Stripe HTTP error: ' . ($err ?: $code) . ' ' . substr((string)$body, 0, 200)];
        }
        $resp = json_decode($body, true);
        if (!is_array($resp) || empty($resp['url'])) {
            return ['action' => 'error', 'message' => 'Stripe init failed: missing checkout URL.'];
        }
        return ['action' => 'redirect', 'url' => $resp['url']];
    }

    public function handle_callback($invoiceId, array $payload)
    {
        $sessionId = $payload['session_id'] ?? null;
        if (!$sessionId) return ['status' => 'failed', 'txn_id' => null, 'raw' => $payload];

        list($code, $body, $err) = $this->http_get(
            'https://api.stripe.com/v1/checkout/sessions/' . rawurlencode($sessionId),
            ['Authorization: Bearer ' . $this->credentials['secret_key']]
        );
        if ($err || $code < 200 || $code >= 300) {
            return ['status' => 'failed', 'txn_id' => $sessionId, 'raw' => ['err' => $err, 'http' => $code, 'body' => $body]];
        }
        $resp = json_decode($body, true);
        if (!is_array($resp)) return ['status' => 'failed', 'txn_id' => $sessionId, 'raw' => $payload];

        $paymentStatus = $resp['payment_status'] ?? 'unpaid';
        if ($paymentStatus === 'paid') {
            return ['status' => 'succeeded', 'txn_id' => $resp['payment_intent'] ?? $sessionId, 'raw' => $resp];
        }
        return ['status' => 'pending', 'txn_id' => $sessionId, 'raw' => $resp];
    }

    public function handle_ipn($invoiceId, array $payload)
    {
        // Stripe webhook event — adapter trusts the controller to have
        // already verified the Stripe-Signature header against
        // credentials.webhook_secret. If you're wiring this in production
        // make sure to verify the signature in the controller layer.
        $type = $payload['type'] ?? '';
        if ($type === 'checkout.session.completed' || $type === 'payment_intent.succeeded') {
            $obj = $payload['data']['object'] ?? [];
            return [
                'status' => 'succeeded',
                'txn_id' => $obj['payment_intent'] ?? $obj['id'] ?? null,
                'raw'    => $payload,
            ];
        }
        if ($type === 'checkout.session.expired' || $type === 'payment_intent.payment_failed') {
            return ['status' => 'failed', 'txn_id' => null, 'raw' => $payload];
        }
        return ['status' => 'pending', 'txn_id' => null, 'raw' => $payload];
    }
}

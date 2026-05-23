<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Saas_gateway_interface — contract every SaaS billing adapter implements.
 *
 * The Saas_billing controller is provider-agnostic: it loads the adapter
 * keyed by `saas_payment_gateway.code`, hands it a fully-hydrated invoice
 * + credentials hash, and delegates the per-provider mechanics to the
 * adapter.
 *
 * Lifecycle
 * ---------
 *   1. initiate($invoice)
 *        Returns either:
 *          - ['action' => 'redirect', 'url' => '<gateway-hosted page>']
 *          - ['action' => 'render',   'html' => '<inline form HTML>']
 *          - ['action' => 'mark_paid','txn_id' => '...']  (manual provider)
 *          - ['action' => 'error',    'message' => '...']
 *   2. handle_callback($invoiceId, $payload)
 *        Called from /billing/success/<provider>, /billing/fail/<provider>,
 *        /billing/cancel/<provider>. Adapter validates the redirect-payload
 *        signature with the gateway and returns:
 *          ['status' => 'succeeded'|'failed'|'cancelled'|'pending',
 *           'txn_id' => '...', 'raw' => mixed]
 *   3. handle_ipn($invoiceId, $payload)
 *        Server-to-server notification. Same return shape as
 *        handle_callback. Many providers (SSLCommerz, bKash, Nagad) call
 *        the IPN endpoint independently of the browser redirect; the
 *        adapter is expected to be idempotent so double-fire is safe.
 *
 * Adapters MUST NOT mutate the DB themselves — they return their verdict
 * and the controller calls Saas_model::markInvoicePaid() once, which is
 * the single point that updates `invoice.status` + inserts `saas_payment`
 * + extends the subscription.
 *
 * @author SmartSchool.bd
 */
interface Saas_gateway_interface
{
    /**
     * Short provider code, must match a row in `saas_payment_gateway.code`.
     * Examples: 'manual', 'sslcommerz', 'stripe', 'bkash', 'nagad',
     * 'rocket', 'paykureghor'.
     */
    public function code();

    /** Human-readable provider name shown in the tenant pay page. */
    public function display_name();

    /**
     * Whether this adapter has enough configuration to attempt a charge.
     * Adapters should validate the credentials hash and return false if
     * required keys are missing. The controller uses this to filter out
     * mis-configured providers from the tenant pay page.
     */
    public function is_configured();

    /**
     * Whether this adapter supports stored-token recurring charges.
     * The renewal cron uses this to decide whether to auto-charge or
     * just email the school admin a pay link. P5.2 v1 ships with
     * invoice-only renewal, so this flag is informational today.
     */
    public function supports_recurring();

    /**
     * Begin a payment for an invoice row.
     *
     * @param object $invoice  the `invoice` table row, with `branch_id`,
     *                         `invoice_no`, `amount`, `currency` populated.
     * @param object $branch   the `branch` table row for the school the
     *                         invoice belongs to.
     * @return array  see lifecycle docblock.
     */
    public function initiate($invoice, $branch);

    /**
     * Handle the browser redirect-back from the gateway.
     *
     * @param int   $invoiceId
     * @param array $payload  $_GET + $_POST merged.
     * @return array  ['status' => ..., 'txn_id' => ..., 'raw' => ...]
     */
    public function handle_callback($invoiceId, array $payload);

    /**
     * Handle the server-to-server IPN.
     *
     * @param int   $invoiceId
     * @param array $payload  parsed request body (form OR JSON).
     * @return array  ['status' => ..., 'txn_id' => ..., 'raw' => ...]
     */
    public function handle_ipn($invoiceId, array $payload);
}

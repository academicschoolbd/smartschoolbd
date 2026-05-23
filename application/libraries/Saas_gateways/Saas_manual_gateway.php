<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Saas_gateway_base.php';

/**
 * Manual / bank-transfer "provider".
 *
 * Doesn't talk to any external API. `initiate()` hands the user off to the
 * dedicated tenant-facing manual flow at /billing/manual/<invoice_id>, which
 * renders the bank/MFS instructions (configured by the super-admin) and
 * accepts a payment-proof submission. The super-admin then approves the
 * submission via /saas/manual_payments, which flips the invoice to paid via
 * Saas_model::markInvoicePaid().
 */
class Saas_manual_gateway extends Saas_gateway_base
{
    protected $code = 'manual';
    protected $name = 'Manual / Bank Transfer';
    protected $supports_recurring = false;
    protected $required_credentials = [];

    public function is_configured()
    {
        // Manual is always usable. Without configured account details the
        // tenant just sees a generic 'contact admin' message on the manual
        // pay page.
        return true;
    }

    public function initiate($invoice, $branch)
    {
        return [
            'action' => 'redirect',
            'url'    => base_url('billing/manual/' . (int)$invoice->id),
        ];
    }
}

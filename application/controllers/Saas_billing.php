<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Saas_billing — tenant-facing pay flow for SaaS invoices.
 *
 * Routes (registered in application/config/routes.php):
 *   GET  /billing/pay/<invoice_id>                 — provider selector
 *   POST /billing/start/<invoice_id>/<provider>    — kick off a charge
 *   GET  /billing/success/<provider>               — gateway redirect-back (success)
 *   GET  /billing/fail/<provider>                  — gateway redirect-back (failed)
 *   GET  /billing/cancel/<provider>                — gateway redirect-back (cancelled)
 *   POST /billing/ipn/<provider>                   — server-to-server IPN
 *
 * Authorisation
 * -------------
 * - The pay-selector + start routes require a logged-in admin/super-admin
 *   whose `branch_id` matches the invoice's branch (super-admins can pay
 *   for any tenant).
 * - The success/fail/cancel/ipn routes are PUBLIC by design — they're
 *   hit by the gateway, not by an authenticated user. They identify the
 *   invoice from the payload and re-verify with the gateway before
 *   marking anything paid (single point of truth = Saas_model::markInvoicePaid).
 *
 * @author SmartSchool.bd
 */
class Saas_billing extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('saas_model');
        $this->load->library('saas_gateway');
        $this->load->helper(['url', 'form', 'language', 'saas_notify']);
    }

    /** Provider selector for a given invoice. */
    public function pay($invoiceId = 0)
    {
        $inv = $this->saas_model->getInvoiceById((int)$invoiceId);
        if (!$inv) show_404();

        $this->require_auth_for_invoice($inv);

        $branch = $this->db->where('id', (int)$inv->branch_id)->get('branch')->row();
        $rows   = $this->saas_gateway->gateway_rows(/*onlyEnabled*/ true);

        // Filter out adapters that report they're not configured.
        $providers = [];
        foreach ($rows as $row) {
            $impl = $this->saas_gateway->get($row->code);
            if ($impl && $impl->is_configured()) {
                $providers[] = ['code' => $row->code, 'name' => $row->name];
            }
        }

        $this->load->view('saas_billing/pay', [
            'invoice'   => $inv,
            'branch'    => $branch,
            'providers' => $providers,
        ]);
    }

    /** POST: start the charge for the chosen provider. */
    public function start($invoiceId = 0, $provider = '')
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('billing/pay/' . (int)$invoiceId));
        }
        $inv = $this->saas_model->getInvoiceById((int)$invoiceId);
        if (!$inv) show_404();
        $this->require_auth_for_invoice($inv);

        if ($inv->status === 'paid') {
            $this->_render_result('Invoice is already paid.', true);
            return;
        }

        $impl = $this->saas_gateway->get($provider);
        if (!$impl) {
            $this->_render_result('Unknown payment provider.', false);
            return;
        }
        if (!$impl->is_configured()) {
            $this->_render_result('Provider is not configured. Please ask the super-admin.', false);
            return;
        }
        $branch = $this->db->where('id', (int)$inv->branch_id)->get('branch')->row();
        $result = $impl->initiate($inv, $branch);

        switch ($result['action'] ?? '') {
            case 'redirect':
                redirect($result['url']);
                return;
            case 'render':
                $this->load->view('saas_billing/result', [
                    'invoice' => $inv,
                    'success' => false,
                    'message' => $result['html'],
                    'html_message' => true,
                ]);
                return;
            case 'mark_paid':
                if (!is_superadmin_loggedin()) {
                    $this->_render_result('Only super-admins can mark invoices paid manually.', false);
                    return;
                }
                $this->saas_model->markInvoicePaid((int)$inv->id, $provider, (string)($result['txn_id'] ?? 'admin-manual'));
                $this->_render_result('Invoice marked paid.', true);
                return;
            case 'error':
            default:
                $this->_render_result($result['message'] ?? 'Provider returned an error.', false);
                return;
        }
    }

    public function success($provider = '') { $this->_finish($provider, 'callback'); }
    public function fail($provider = '')    { $this->_finish($provider, 'callback'); }
    public function cancel($provider = '')  { $this->_finish($provider, 'callback'); }

    // -------------------------------------------------------------------------
    // Manual / bank-transfer flow
    // -------------------------------------------------------------------------

    /** GET /billing/manual/<invoice_id> — render instructions + submission form. */
    public function manual($invoiceId = 0)
    {
        $inv = $this->saas_model->getInvoiceById((int)$invoiceId);
        if (!$inv) show_404();
        $this->require_auth_for_invoice($inv);

        $this->load->model('saas_payment_gateway_model');
        $this->load->model('saas_manual_payment_submission_model');
        $this->load->model('saas_setting_model');

        $manualCfg = $this->saas_payment_gateway_model->getCredentials('manual') ?: [];
        $branch    = $this->db->where('id', (int)$inv->branch_id)->get('branch')->row();
        $existing  = $this->saas_manual_payment_submission_model->getForBranch((int)$inv->branch_id, 20);

        // Filter the existing list to this invoice only for clarity.
        $forThisInvoice = array_values(array_filter($existing, function ($r) use ($inv) {
            return (int)$r->invoice_id === (int)$inv->id;
        }));

        $this->load->view('saas_billing/manual_pay', [
            'invoice'      => $inv,
            'branch'       => $branch,
            'manual'       => $manualCfg,
            'submissions'  => $forThisInvoice,
            'has_pending'  => $this->saas_manual_payment_submission_model->hasPendingForInvoice((int)$inv->id),
            'billing_contact_email' => $this->saas_setting_model->get('billing_contact_email', 'billing@smartschool.bd'),
        ]);
    }

    /** POST /billing/submit_manual/<invoice_id> — tenant submits proof of transfer. */
    public function submit_manual($invoiceId = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('billing/manual/' . (int)$invoiceId));
        }
        $inv = $this->saas_model->getInvoiceById((int)$invoiceId);
        if (!$inv) show_404();
        $this->require_auth_for_invoice($inv);

        if ($inv->status === 'paid') {
            $this->_render_result('Invoice is already paid.', true);
            return;
        }

        $this->load->model('saas_manual_payment_submission_model');

        if ($this->saas_manual_payment_submission_model->hasPendingForInvoice((int)$inv->id)) {
            set_alert('error', 'A payment proof is already pending review for this invoice.');
            redirect(base_url('billing/manual/' . (int)$inv->id));
        }

        $txnRef = trim((string)$this->input->post('txn_ref'));
        if ($txnRef === '') {
            set_alert('error', 'Please enter the transaction reference / receipt number.');
            redirect(base_url('billing/manual/' . (int)$inv->id));
        }

        $screenshotPath = $this->_upload_manual_screenshot((int)$inv->branch_id);
        // _upload_manual_screenshot() returns null if no file was uploaded; on
        // upload failure it already set an alert and we redirected away.

        $submissionId = $this->saas_manual_payment_submission_model->create([
            'invoice_id'      => (int)$inv->id,
            'branch_id'       => (int)$inv->branch_id,
            'txn_ref'         => $txnRef,
            'payer_name'      => trim((string)$this->input->post('payer_name'))   ?: null,
            'payer_phone'     => trim((string)$this->input->post('payer_phone'))  ?: null,
            'payer_account'   => trim((string)$this->input->post('payer_account'))?: null,
            'paid_at'         => $this->_normalise_date($this->input->post('paid_at')),
            'amount'          => (float)$inv->amount,
            'screenshot_path' => $screenshotPath,
            'notes'           => trim((string)$this->input->post('notes')) ?: null,
            'status'          => 'pending',
        ]);

        // Fire-and-forget Telegram notification.
        $submission = $this->saas_manual_payment_submission_model->getById($submissionId);
        $branch     = $this->db->where('id', (int)$inv->branch_id)->get('branch')->row();
        if ($submission && $branch) saas_notify_manual_pending($submission, $inv, $branch);

        $this->load->view('saas_billing/manual_submitted', [
            'invoice'    => $inv,
            'submission' => $submission,
        ]);
    }

    /** Handle screenshot upload; returns the relative path or null. */
    protected function _upload_manual_screenshot($branchId)
    {
        if (empty($_FILES['screenshot']['name'])) return null;

        $dir = FCPATH . 'uploads/saas_manual_payments/' . (int)$branchId . '/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        $this->load->library('upload', [
            'upload_path'   => $dir,
            'allowed_types' => 'gif|jpg|jpeg|png|pdf',
            'max_size'      => 4096,
            'encrypt_name'  => true,
        ]);
        if (!$this->upload->do_upload('screenshot')) {
            set_alert('error', 'Upload failed: ' . strip_tags($this->upload->display_errors('', ' ')));
            redirect(current_url(), 'refresh');
        }
        $data = $this->upload->data();
        return 'uploads/saas_manual_payments/' . (int)$branchId . '/' . $data['file_name'];
    }

    protected function _normalise_date($input)
    {
        $input = trim((string)$input);
        if ($input === '') return null;
        $ts = strtotime($input);
        return $ts ? date('Y-m-d', $ts) : null;
    }

    /** Server-to-server IPN. Always responds 200 even on failure so the
     *  gateway doesn't keep retrying after we've recorded the verdict. */
    public function ipn($provider = '')
    {
        $payload = $this->_merged_payload();
        $invoiceId = $this->_invoice_id_from_payload($payload);
        if (!$invoiceId) {
            // Some gateways re-encode the body as raw JSON; try to parse it.
            $raw = file_get_contents('php://input');
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $payload = array_merge($payload, $decoded);
                    $invoiceId = $this->_invoice_id_from_payload($payload);
                }
            }
        }
        $impl = $this->saas_gateway->get($provider);
        if (!$impl || !$invoiceId) {
            $this->output->set_status_header(200)->set_output('IGNORED');
            return;
        }
        $inv = $this->saas_model->getInvoiceById((int)$invoiceId);
        if (!$inv) {
            $this->output->set_status_header(200)->set_output('IGNORED');
            return;
        }
        $res = $impl->handle_ipn((int)$inv->id, $payload);
        if (($res['status'] ?? '') === 'succeeded' && $inv->status !== 'paid') {
            $this->saas_model->markInvoicePaid((int)$inv->id, $provider, (string)($res['txn_id'] ?? ''), $res['raw'] ?? $payload);
            $branch = $this->db->where('id', (int)$inv->branch_id)->get('branch')->row();
            saas_notify_payment_paid($inv, $branch, $provider, $res['txn_id'] ?? null);
        }
        $this->output->set_status_header(200)->set_output('OK');
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    protected function _finish($provider, $kind)
    {
        $payload = $this->_merged_payload();
        $invoiceId = $this->_invoice_id_from_payload($payload);
        $impl      = $this->saas_gateway->get($provider);

        if (!$impl || !$invoiceId) {
            $this->_render_result('Payment status unknown — please contact support.', false);
            return;
        }
        $inv = $this->saas_model->getInvoiceById((int)$invoiceId);
        if (!$inv) {
            $this->_render_result('Invoice not found.', false);
            return;
        }
        $res = $impl->handle_callback((int)$inv->id, $payload);
        if (($res['status'] ?? '') === 'succeeded' && $inv->status !== 'paid') {
            $this->saas_model->markInvoicePaid((int)$inv->id, $provider, (string)($res['txn_id'] ?? ''), $res['raw'] ?? $payload);
            $branch = $this->db->where('id', (int)$inv->branch_id)->get('branch')->row();
            saas_notify_payment_paid($inv, $branch, $provider, $res['txn_id'] ?? null);
        }
        $message = $res['status'] ?? 'failed';
        switch ($message) {
            case 'succeeded':
                $this->_render_result('Payment received. Subscription extended.', true);
                return;
            case 'cancelled':
                $this->_render_result('Payment cancelled.', false);
                return;
            case 'pending':
                $this->_render_result('Payment is still being processed. We will update the invoice as soon as the gateway confirms.', false);
                return;
            case 'failed':
            default:
                $this->_render_result('Payment failed. You can try again from the invoice page.', false);
                return;
        }
    }

    protected function _render_result($message, $success)
    {
        // Try to load the invoice from the payload so the result page can
        // show a "Back to invoice" link; falling back to null is fine.
        $invoiceId = $this->_invoice_id_from_payload($this->_merged_payload());
        $inv = $invoiceId ? $this->saas_model->getInvoiceById((int)$invoiceId) : null;
        $this->load->view('saas_billing/result', [
            'invoice'      => $inv,
            'success'      => (bool)$success,
            'message'      => $message,
            'html_message' => false,
        ]);
    }

    protected function _merged_payload()
    {
        return array_merge($_GET ?: [], $_POST ?: []);
    }

    /** Locate the invoice id from a gateway payload. */
    protected function _invoice_id_from_payload(array $payload)
    {
        // 1. Explicit invoice_id query param (Stripe, Nagad, Rocket adapters set this).
        if (!empty($payload['invoice_id'])) return (int)$payload['invoice_id'];
        // 2. SSLCommerz round-trips it as value_a.
        if (!empty($payload['value_a']))    return (int)$payload['value_a'];
        // 3. Stripe metadata.
        if (!empty($payload['metadata']['invoice_id'])) return (int)$payload['metadata']['invoice_id'];
        if (!empty($payload['data']['object']['metadata']['invoice_id'])) {
            return (int)$payload['data']['object']['metadata']['invoice_id'];
        }
        // 4. Last resort: look up by invoice_no.
        $invoiceNo = $payload['tran_id'] ?? $payload['order_id'] ?? $payload['merchantInvoiceNumber'] ?? null;
        if ($invoiceNo) {
            $row = $this->db->where('invoice_no', $invoiceNo)->get('invoice')->row();
            if ($row) return (int)$row->id;
        }
        return 0;
    }

    /** Enforce that the current session may pay this invoice. */
    protected function require_auth_for_invoice($inv)
    {
        if (is_superadmin_loggedin()) return;
        if (is_admin_loggedin() && (int)get_loggedin_branch_id() === (int)$inv->branch_id) return;
        $this->session->set_userdata('last_page', current_url());
        redirect(base_url(), 'refresh');
    }
}

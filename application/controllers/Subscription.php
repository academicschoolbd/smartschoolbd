<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Subscription — Tenant-facing controller. Shows the current plan, usage, and
 * upcoming renewal for the logged-in tenant's branch.
 *
 * Routes (referenced by saas_menu.php):
 *   /subscription/index   — tenant dashboard view
 *   /subscription/upgrade — pick a higher plan (POST: assign + create invoice)
 *
 * @author SmartSchool.bd
 */
class Subscription extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('saas_model');
        if (!is_admin_loggedin() && !is_superadmin_loggedin()) {
            $this->session->set_userdata('last_page', current_url());
            redirect(base_url(), 'refresh');
        }
    }

    public function index()
    {
        $branchId = (int)$this->session->userdata('loggedin_branch');
        $this->data['title']        = translate('subscription');
        $this->data['main_menu']    = 'subscription';
        $this->data['sub_page']     = 'subscription/index';
        $this->data['subscription'] = $this->saas_model->getSubscriptionByBranch($branchId);
        $this->data['packages']     = $this->saas_model->getPackages(true);
        $this->data['invoices']     = $this->saas_model->getInvoicesForBranch($branchId);
        $this->data['payments']     = $this->saas_model->getPaymentsForBranch($branchId);
        $this->data['usage']        = $this->saas_model->getUsageStats($branchId);
        $this->load->view('layout/index', $this->data);
    }

    public function upgrade()
    {
        $branchId  = (int)$this->session->userdata('loggedin_branch');
        $packageId = (int)$this->input->post('package_id');
        $pkg = $this->saas_model->getPackageById($packageId);
        if (!$pkg) { set_alert('error', translate('plan_not_found')); redirect(base_url('subscription')); }

        // Assign immediately as 'past_due' (becomes active once invoice paid).
        // For free plan, no invoice needed.
        if ((float)$pkg->price_bdt <= 0) {
            $this->saas_model->assignPackage($branchId, $packageId, 'active');
            set_alert('success', translate('plan_changed'));
        } else {
            $subId = $this->saas_model->assignPackage($branchId, $packageId, 'past_due');
            $sub = $this->saas_model->getSubscriptionByBranch($branchId);
            $invoiceId = $this->saas_model->createInvoice(
                $branchId,
                $sub ? (int)$sub->id : $subId,
                (float)$pkg->price_bdt,
                date('Y-m-d'),
                date('Y-m-d', strtotime('+30 days'))
            );
            set_alert('success', translate('invoice_created_pay_to_activate'));
            // Send the user straight to the pay page for the new invoice.
            if ($invoiceId > 0) {
                redirect(base_url('billing/pay/' . (int)$invoiceId));
            }
        }
        redirect(base_url('subscription'));
    }
}

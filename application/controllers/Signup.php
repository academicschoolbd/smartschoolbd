<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Signup — Public self-service signup form for new tenants.
 *
 * Routes:
 *   /signup           — GET: form, POST: submit
 *   /signup/thanks    — confirmation page
 *   /signup/check_subdomain/<sd> — JSON: {available: bool}
 *
 * Approval flow:
 *   1. Visitor fills /signup → row inserted into `saas_pending_request`.
 *   2. Super-admin reviews at /saas/pending_request.
 *   3. Super-admin clicks Approve → Saas::approve() creates branch + subscription + custom_domain.
 *   4. Notification email is sent to owner with their subdomain URL.
 *
 * @author SmartSchool.bd
 */
class Signup extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('saas_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->input->post('submit') === 'apply') {
            $this->form_validation->set_rules('school_name',  'School Name',     'required|max_length[255]');
            $this->form_validation->set_rules('subdomain',    'Subdomain',       'required|min_length[3]|max_length[64]|alpha_dash|callback_unique_subdomain');
            $this->form_validation->set_rules('owner_name',   'Your Name',       'required|max_length[255]');
            $this->form_validation->set_rules('owner_email',  'Email',           'required|valid_email');
            $this->form_validation->set_rules('owner_phone',  'Phone',           'required|max_length[64]');
            $this->form_validation->set_rules('package_id',   'Plan',            'required|integer');
            $this->form_validation->set_rules('terms_accept', 'Terms of Service', 'required');

            if ($this->form_validation->run() === true) {
                $data = [
                    'school_name'    => $this->input->post('school_name'),
                    'school_name_bn' => $this->input->post('school_name_bn'),
                    'subdomain'      => strtolower(trim($this->input->post('subdomain'))),
                    'owner_name'     => $this->input->post('owner_name'),
                    'owner_email'    => $this->input->post('owner_email'),
                    'owner_phone'    => $this->input->post('owner_phone'),
                    'package_id'     => (int)$this->input->post('package_id'),
                    'status'         => 'pending',
                    'notes'          => $this->input->post('notes'),
                ];
                $this->saas_model->savePendingRequest($data);
                redirect(base_url('signup/thanks'));
            }
        }

        $this->data['packages'] = $this->saas_model->getPackages(true);
        $this->data['title']    = 'Sign up — SmartSchool.bd';
        $this->load->view('signup/index', $this->data);
    }

    public function thanks()
    {
        $this->data['title'] = 'Application received — SmartSchool.bd';
        $this->load->view('signup/thanks', $this->data);
    }

    public function check_subdomain($sd = '')
    {
        header('Content-Type: application/json');
        echo json_encode(['available' => !$this->saas_model->isSubdomainTaken($sd)]);
    }

    // -- form_validation callback
    public function unique_subdomain($sd)
    {
        if ($this->saas_model->isSubdomainTaken($sd)) {
            $this->form_validation->set_message('unique_subdomain', 'This subdomain is already taken or reserved.');
            return false;
        }
        return true;
    }
}

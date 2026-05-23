<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Custom_domain — Super-admin + tenant admin management of `custom_domain` rows.
 *
 * The `custom_domain` table maps host names (`<sub>.smartschool.bd`, or a
 * tenant-owned domain like `app.theirschool.com`) to a `branch.id`.
 *
 * Super-admin routes (Admin_Controller, role 1):
 *   /custom_domain                       — alias for /custom_domain/list
 *   /custom_domain/list                  — list every mapping across tenants
 *   /custom_domain/add                   — GET form / POST insert
 *   /custom_domain/edit/<id>             — GET form / POST update
 *   /custom_domain/delete/<id>           — POST delete
 *   /custom_domain/toggle/<id>           — POST flip status 0/1
 *   /custom_domain/verify/<id>           — POST re-check CNAME / mapping
 *
 * Tenant admin routes (Admin_Controller, role 2):
 *   /custom_domain/mylist                — only this tenant's domains
 *   /custom_domain/myadd                 — request a new custom domain
 *   /custom_domain/dns_instruction/<id>  — show DNS config instructions
 *
 * @author SmartSchool.bd
 */
class Custom_domain extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('custom_domain_model');
        if (!$this->db->table_exists('custom_domain')) {
            show_error('custom_domain table is missing. Apply migration 002_custom_domain_and_saas.sql first.', 500);
        }
    }

    private function require_superadmin()
    {
        if (!is_superadmin_loggedin()) {
            set_alert('error', translate('access_denied'));
            redirect(base_url('dashboard'));
        }
    }

    // -------------------------------------------------------------------------
    // Super-admin: full list
    // -------------------------------------------------------------------------
    public function index()
    {
        redirect(base_url('custom_domain/list'));
    }

    public function list()
    {
        $this->require_superadmin();
        $this->data['title']    = translate('custom_domain');
        $this->data['main_menu'] = 'custom_domain';
        $this->data['sub_page']  = 'custom_domain/list';
        $this->data['rows']      = $this->custom_domain_model->getAll();
        $this->data['branches']  = $this->db->select('id, name, subdomain')->order_by('name', 'asc')->get('branch')->result();
        $this->load->view('layout/index', $this->data);
    }

    public function add()
    {
        $this->require_superadmin();
        if ($this->input->post('submit') === 'save') {
            $data = [
                'school_id'   => (int)$this->input->post('school_id'),
                'url'         => strtolower(trim((string)$this->input->post('url'))),
                'domain_type' => $this->input->post('domain_type') ?: 'subdomain',
                'status'      => $this->input->post('status') ? 1 : 0,
                'notes'       => (string)$this->input->post('notes'),
            ];
            if ($data['school_id'] && $data['url']) {
                $this->custom_domain_model->insert($data);
                set_alert('success', translate('information_has_been_saved_successfully'));
            } else {
                set_alert('error', translate('please_fill_in_all_required_fields'));
            }
            redirect(base_url('custom_domain/list'));
        }
        $this->data['title']    = translate('custom_domain');
        $this->data['main_menu'] = 'custom_domain';
        $this->data['sub_page']  = 'custom_domain/add';
        $this->data['row']       = null;
        $this->data['branches']  = $this->db->select('id, name, subdomain')->order_by('name', 'asc')->get('branch')->result();
        $this->load->view('layout/index', $this->data);
    }

    public function edit($id = 0)
    {
        $this->require_superadmin();
        $id = (int)$id;
        $row = $this->custom_domain_model->getById($id);
        if (!$row) {
            set_alert('error', translate('information_not_found'));
            redirect(base_url('custom_domain/list'));
        }
        if ($this->input->post('submit') === 'save') {
            $data = [
                'school_id'   => (int)$this->input->post('school_id'),
                'url'         => strtolower(trim((string)$this->input->post('url'))),
                'domain_type' => $this->input->post('domain_type') ?: 'subdomain',
                'status'      => $this->input->post('status') ? 1 : 0,
                'notes'       => (string)$this->input->post('notes'),
            ];
            $this->custom_domain_model->update($id, $data);
            set_alert('success', translate('information_has_been_updated_successfully'));
            redirect(base_url('custom_domain/list'));
        }
        $this->data['title']    = translate('custom_domain');
        $this->data['main_menu'] = 'custom_domain';
        $this->data['sub_page']  = 'custom_domain/add';
        $this->data['row']       = $row;
        $this->data['branches']  = $this->db->select('id, name, subdomain')->order_by('name', 'asc')->get('branch')->result();
        $this->load->view('layout/index', $this->data);
    }

    public function delete($id = 0)
    {
        $this->require_superadmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('custom_domain/list'));
        }
        $this->custom_domain_model->delete((int)$id);
        set_alert('success', translate('information_has_been_deleted_successfully'));
        redirect(base_url('custom_domain/list'));
    }

    public function toggle($id = 0)
    {
        $this->require_superadmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('custom_domain/list'));
        }
        $this->custom_domain_model->toggleStatus((int)$id);
        set_alert('success', translate('information_has_been_updated_successfully'));
        redirect(base_url('custom_domain/list'));
    }

    public function verify($id = 0)
    {
        $this->require_superadmin();
        $row = $this->custom_domain_model->getById((int)$id);
        if (!$row) {
            set_alert('error', translate('information_not_found'));
            redirect(base_url('custom_domain/list'));
        }
        $verdict = $this->custom_domain_model->probe($row->url);
        $message = $verdict['ok']
            ? translate('information_has_been_verified') . ' (' . $verdict['detail'] . ')'
            : translate('verification_failed') . ' (' . $verdict['detail'] . ')';
        set_alert($verdict['ok'] ? 'success' : 'error', $message);
        if ($verdict['ok']) {
            $this->custom_domain_model->update((int)$id, ['status' => 1]);
        }
        redirect(base_url('custom_domain/list'));
    }

    // -------------------------------------------------------------------------
    // Tenant admin: only see / request their own
    // -------------------------------------------------------------------------
    public function mylist()
    {
        if (is_superadmin_loggedin()) {
            redirect(base_url('custom_domain/list'));
        }
        $branchID = (int)$this->session->userdata('loggedin_branch');
        if (!$branchID) {
            $branchID = (int)$this->application_model->get_branch_id();
        }
        $this->data['title']    = translate('custom_domain');
        $this->data['main_menu'] = 'domain_request';
        $this->data['sub_page']  = 'custom_domain/mylist';
        $this->data['rows']      = $this->custom_domain_model->getByBranch($branchID);
        $this->load->view('layout/index', $this->data);
    }

    public function myadd()
    {
        if (is_superadmin_loggedin()) {
            redirect(base_url('custom_domain/list'));
        }
        $branchID = (int)$this->session->userdata('loggedin_branch');
        if (!$branchID) {
            $branchID = (int)$this->application_model->get_branch_id();
        }
        if (!$branchID) {
            set_alert('error', translate('access_denied'));
            redirect(base_url('dashboard'));
        }

        if ($this->input->post('submit') === 'save') {
            $url = strtolower(trim((string)$this->input->post('url')));
            if (!$url) {
                set_alert('error', translate('please_fill_in_all_required_fields'));
                redirect(base_url('custom_domain/myadd'));
            }
            $this->custom_domain_model->insert([
                'school_id'   => $branchID,
                'url'         => $url,
                'domain_type' => 'custom',
                'status'      => 0,
                'notes'       => 'Requested by tenant admin · ' . date('Y-m-d H:i:s'),
            ]);
            $newId = (int)$this->db->insert_id();
            set_alert('success', translate('domain_request_submitted'));
            redirect(base_url('custom_domain/dns_instruction/' . $newId));
        }

        $this->data['title']    = translate('custom_domain');
        $this->data['main_menu'] = 'domain_request';
        $this->data['sub_page']  = 'custom_domain/myadd';
        $this->load->view('layout/index', $this->data);
    }

    public function dns_instruction($id = 0)
    {
        $row = $this->custom_domain_model->getById((int)$id);
        if (!$row) {
            set_alert('error', translate('information_not_found'));
            redirect(base_url(is_superadmin_loggedin() ? 'custom_domain/list' : 'custom_domain/mylist'));
        }
        // Tenant admins can only view their own
        if (!is_superadmin_loggedin()) {
            $branchID = (int)$this->session->userdata('loggedin_branch');
            if ((int)$row->school_id !== $branchID) {
                set_alert('error', translate('access_denied'));
                redirect(base_url('custom_domain/mylist'));
            }
        }
        $this->data['title']    = translate('dns_instruction');
        $this->data['main_menu'] = is_superadmin_loggedin() ? 'custom_domain' : 'domain_request';
        $this->data['sub_page']  = 'custom_domain/dns_instruction';
        $this->data['row']       = $row;
        $this->load->view('layout/index', $this->data);
    }
}

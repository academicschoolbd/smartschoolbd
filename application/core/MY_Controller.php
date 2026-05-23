<?php defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        if ($this->config->item('installed') == false) {
            redirect(site_url('install'));
        }

        $get_config = $this->db->get_where('global_settings', array('id' => 1))->row_array();
        $branchID = $this->application_model->get_branch_id();
        if (!empty($branchID)) {
            $branch = $this->db->select('currency_formats,symbol_position,symbol,currency,timezone')->where('id', $branchID)->get('branch')->row();
            $get_config['currency'] = $branch->currency;
            $get_config['currency_symbol'] = $branch->symbol;
            $get_config['currency_formats'] = $branch->currency_formats;
            $get_config['symbol_position'] = $branch->symbol_position;
            if (!empty($branch->timezone)) {
                $get_config['timezone'] = $branch->timezone;
            }
        }
        $this->data['global_config'] = $get_config;
        $this->data['theme_config'] = $this->db->get_where('theme_settings', array('id' => 1))->row_array();

        date_default_timezone_set($get_config['timezone']);
    }

    public function get_payment_config()
    {
        $branchID = $this->application_model->get_branch_id();
        $this->db->where('branch_id', $branchID);
        $this->db->select('*')->from('payment_config');
        return $this->db->get()->row_array();
    }

    public function getBranchDetails()
    {
        $branchID = $this->application_model->get_branch_id();
        $this->db->select('*');
        $this->db->where('id', $branchID);
        $this->db->from('branch');
        $r = $this->db->get()->row_array();
        if (empty($r)) {
            return ['stu_generate' => "", 'grd_generate' => ""];
        } else {
            return $r;
        }
    }

    public function photoHandleUpload($str, $fields)
    {
        $allowedExts = array_map('trim', array_map('strtolower', explode(',', $this->data['global_config']['image_extension'])));
        $allowedSizeKB = $this->data['global_config']['image_size'];
        $allowedSize = floatval(1024 * $allowedSizeKB);
        if (isset($_FILES["$fields"]) && !empty($_FILES["$fields"]['name'])) {
            $file_size = $_FILES["$fields"]["size"];
            $file_name = $_FILES["$fields"]["name"];
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($files = filesize($_FILES["$fields"]['tmp_name'])) {
                if (!in_array(strtolower($extension), $allowedExts)) {
                    $this->form_validation->set_message('photoHandleUpload', translate('this_file_type_is_not_allowed'));
                    return false;
                }
                if ($file_size > $allowedSize) {
                    $this->form_validation->set_message('photoHandleUpload', translate('file_size_shoud_be_less_than') . " $allowedSizeKB KB.");
                    return false;
                }
            } else {
                $this->form_validation->set_message('photoHandleUpload', translate('error_reading_the_file'));
                return false;
            }
            return true;
        }
    }

    public function fileHandleUpload($str, $fields)
    {
        $allowedExts = array_map('trim', array_map('strtolower', explode(',', $this->data['global_config']['file_extension'])));
        $allowedSizeKB = $this->data['global_config']['file_size'];
        $allowedSize = floatval(1024 * $allowedSizeKB);
        if (isset($_FILES["$fields"]) && !empty($_FILES["$fields"]['name'])) {
            $file_size = $_FILES["$fields"]["size"];
            $file_name = $_FILES["$fields"]["name"];
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($files = filesize($_FILES["$fields"]['tmp_name'])) {
                if (!in_array(strtolower($extension), $allowedExts)) {
                    $this->form_validation->set_message('fileHandleUpload', translate('this_file_type_is_not_allowed'));
                    return false;
                }
                if ($file_size > $allowedSize) {
                    $this->form_validation->set_message('fileHandleUpload', translate('file_size_shoud_be_less_than') . " $allowedSizeKB KB.");
                    return false;
                }
            } else {
                $this->form_validation->set_message('fileHandleUpload', translate('error_reading_the_file'));
                return false;
            }
            return true;
        }
    }
}

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_loggedin()) {
            $this->session->set_userdata('redirect_url', current_url());
            redirect(base_url('authentication'), 'refresh');
        }
        $this->enforce_subdomain_branch_isolation();
    }

    /**
     * P4.3 — Tighten subdomain → branch_id pinning for admin routes.
     *
     * When a request hits a host that resolves to a tenant via
     * custom_domain.url, the logged-in user MUST belong to that branch.
     * Super-admins (role_id=1) are exempt by default, governed by the
     * `strict_subdomain_isolation` config flag.
     *
     * Apex / un-pinned hosts (no custom_domain row) skip the check so the
     * marketing site, signup, and the super-admin URL-alias path keep
     * working as today.
     */
    protected function enforce_subdomain_branch_isolation()
    {
        if (!$this->db->table_exists('custom_domain')) return;

        $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
        $host = preg_replace('/:\d+$/', '', $host);
        $host = preg_replace('/^www\./', '', $host);
        if ($host === '') return;

        $pinned = $this->db
            ->select('school_id')
            ->where('url', $host)
            ->where('status', 1)
            ->limit(1)
            ->get('custom_domain')
            ->row();
        if (!$pinned) return; // apex / unpinned host — no constraint

        $sessionBranch = (int) $this->session->userdata('loggedin_branch');
        $roleID        = (int) $this->session->userdata('loggedin_role_id');
        $strict        = (bool) ($this->config->item('strict_subdomain_isolation') ?? false);

        // Loose mode (default): super-admin can administer any tenant from
        // any subdomain. Flip the config flag to lock super-admin to the
        // pinned tenant too.
        if (!$strict && $roleID === 1) return;

        if ($sessionBranch !== (int) $pinned->school_id) {
            $this->session->set_flashdata('error',
                'You are not allowed to administer this tenant from this URL.');
            $this->session->sess_destroy();
            redirect('https://' . $host . '/authentication');
            exit;
        }
    }
}

class User_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_student_loggedin() && !is_parent_loggedin()) {
            $this->session->set_userdata('redirect_url', current_url());
            redirect(base_url('authentication'), 'refresh');
        }
    }
}

class Authentication_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('authentication_model');
    }
}

class Frontend_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
        $branchID = $this->home_model->getDefaultBranch();
        $cms_setting = $this->db->get_where('front_cms_setting', array('branch_id' => $branchID))->row_array();

        // If the current host is pinned to a tenant via `custom_domain`
        // but the tenant has no `front_cms_setting` row (e.g. the row
        // failed to insert during /saas/approve due to strict-mode SQL),
        // self-heal by creating a sensible default row so the public site
        // renders instead of redirecting the visitor to /authentication —
        // which used to make freshly-approved subdomains look broken.
        if (empty($cms_setting) && $this->_host_is_pinned_tenant($branchID)) {
            $cms_setting = $this->_seed_default_cms_setting((int) $branchID);
        }

        if (empty($cms_setting) || !$cms_setting['cms_active']) {
            redirect(site_url('authentication'));
        }
        $this->data['cms_setting'] = $cms_setting;
    }

    /**
     * True when the current HTTP host is mapped to $branchID via an
     * active `custom_domain` row. Used to distinguish a real tenant
     * subdomain (where we should self-heal) from the legacy
     * url_alias / apex fallback path (where the historical redirect
     * to /authentication is still the correct behaviour).
     */
    private function _host_is_pinned_tenant($branchID)
    {
        if (empty($branchID) || !$this->db->table_exists('custom_domain')) {
            return false;
        }
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
        $host = preg_replace('/:\d+$/', '', $host);
        if ($host === '') {
            return false;
        }
        return (bool) $this->db
            ->where('url', $host)
            ->where('school_id', (int) $branchID)
            ->where('status', 1)
            ->limit(1)
            ->count_all_results('custom_domain');
    }

    /**
     * Self-heal a tenant whose `front_cms_setting` row never got
     * written. Reuses the same Tenant_provisioning_model clone as
     * /saas/approve so the recovered subdomain inherits the template
     * tenant's theme / colours / logos / layout exactly instead of
     * falling back to a generic placeholder. If no template tenant is
     * available (e.g. a brand-new install where this branch IS the
     * first one), we synthesise a minimal placeholder row that
     * populates every NOT NULL column so the INSERT survives MySQL
     * strict mode and the public site renders.
     */
    private function _seed_default_cms_setting($branchID)
    {
        $branch = $this->db->get_where('branch', array('id' => $branchID))->row();
        $slug   = $branch->subdomain ?? $branch->slug ?? ('branch_' . $branchID);
        $name   = $branch->name ?? $branch->school_name ?? ('Branch ' . $branchID);

        $overrides = [
            'application_title' => $name,
            'url_alias'         => preg_replace('/[^a-z0-9_-]/', '_', strtolower($slug)),
            'email'             => (string) ($branch->email ?? ''),
            'mobile_no'         => (string) ($branch->mobileno ?? ''),
            'address'           => (string) ($branch->address ?? ''),
            'copyright_text'    => '© ' . date('Y') . ' ' . $name . ' — Powered by SmartSchool.bd',
        ];

        $this->load->model('tenant_provisioning_model');
        $cloned = $this->tenant_provisioning_model->cloneFrontendForTenant((int) $branchID, $overrides);

        if (!$cloned) {
            $row = array_merge($overrides, [
                'branch_id'            => (int) $branchID,
                'theme'                => 'red',
                'captcha_status'       => 'disable',
                'recaptcha_site_key'   => '',
                'recaptcha_secret_key' => '',
                'fav_icon'             => '',
                'primary_color'        => '#2e7d32',
                'cms_active'           => 1,
            ]);
            $this->db->insert('front_cms_setting', $row);
            return $row;
        }

        return $this->db->get_where('front_cms_setting', array('branch_id' => $branchID))->row_array();
    }
}

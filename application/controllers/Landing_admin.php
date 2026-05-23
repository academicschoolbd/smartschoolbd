<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Landing_admin — super-admin editor for the apex landing page.
 *
 * Mounted at `/saas/landing` (see routes.php).  Mirrors the auth + layout
 * convention used by `Saas` (Admin_Controller → set $title/$main_menu/
 * $sub_page → render `layout/index`, which auto-loads $sub_page).
 *
 * @author SmartSchool.bd
 */
class Landing_admin extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('landing_model');
    }

    /** Editor form. */
    public function index()
    {
        $this->data['title']     = 'Landing page';
        $this->data['main_menu'] = 'landing';
        $this->data['sub_page']  = 'landing_admin/index';
        $this->data['s']         = $this->landing_model->get();
        $this->load->view('layout/index', $this->data);
    }

    /** POST → save. */
    public function save()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            redirect(base_url('saas/landing'));
        }

        $input = (array)$this->input->post();
        unset($input['ci_csrf_token'], $input['save']);

        $uid = $this->session->userdata('login_user_id')
            ? (int)$this->session->userdata('login_user_id')
            : null;

        $ok = $this->landing_model->save($input, $uid);
        if (function_exists('set_alert')) {
            set_alert($ok ? 'success' : 'error',
                $ok ? 'Landing page settings saved.' : 'Could not save settings.');
        } else {
            $this->session->set_flashdata($ok ? 'alert-message-success' : 'alert-message-error',
                $ok ? 'Landing page settings saved.' : 'Could not save settings.');
        }
        redirect(base_url('saas/landing'));
    }

    /** Convenience: flip active variant via GET. */
    public function set_variant($v = 'a')
    {
        $v = in_array($v, ['a','b'], true) ? $v : 'a';
        $uid = $this->session->userdata('login_user_id')
            ? (int)$this->session->userdata('login_user_id')
            : null;
        $this->landing_model->save(['active_variant' => $v], $uid);
        if (function_exists('set_alert')) {
            set_alert('success', 'Active variant set to ' . strtoupper($v) . '.');
        }
        redirect(base_url('saas/landing'));
    }

    /** Quick preview link — redirects to apex landing with override. */
    public function preview($v = 'a')
    {
        $v = in_array($v, ['a','b'], true) ? $v : 'a';
        redirect(base_url('landing?variant=' . $v));
    }
}

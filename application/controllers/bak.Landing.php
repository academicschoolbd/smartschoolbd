<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Landing — public marketing page for the SmartSchool.bd apex domain.
 *
 * Reads landing_setting (singleton, id=1) to decide which variant to render
 * and which copy/colour/section flags to apply.  Falls back to baked-in
 * defaults if the table is missing or empty so the page is never blank.
 *
 * Routing: routes.php sets `default_controller = 'landing'` only when the
 * request host is `smartschool.bd` (or `www.smartschool.bd`); tenant
 * subdomains keep hitting Home.  Also reachable as `/landing` on any host
 * for preview.
 *
 * `?variant=a` / `?variant=b` querystrings override the saved variant for
 * a single request so the admin can preview the unsaved variant before
 * switching it live.
 *
 * @author SmartSchool.bd
 */
class Landing extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('landing_model');
    }

    public function index()
    {
        $settings = $this->landing_model->get();

        // Allow ?variant=a or ?variant=b override for preview.
        $override = strtolower((string)$this->input->get('variant'));
        if (in_array($override, ['a','b'], true)) {
            $settings->active_variant = $override;
        }

        $variant = $settings->active_variant === 'b' ? 'variant_b' : 'variant_a';

        $data = [
            'page_title' => 'SmartSchool.bd — Free school management software for Bangladesh',
            'signup_url' => base_url('signup'),
            'login_url'  => base_url('authentication'),
            's'          => $settings,
        ];
        $this->load->view('landing/' . $variant, $data);
    }
}

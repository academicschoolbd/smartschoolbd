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
 * `?variant=a` … `?variant=e` querystrings override the saved variant for
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

        // Allow ?variant=a..e override for preview.
        $override = strtolower((string)$this->input->get('variant'));
        if (in_array($override, ['a','b','c','d','e'], true)) {
            $settings->active_variant = $override;
        }

        $variant_map = [
            'a' => 'variant_a',
            'b' => 'variant_b',
            'c' => 'variant_c',
            'd' => 'variant_d',
            'e' => 'variant_e',
        ];
        $key = isset($variant_map[$settings->active_variant]) ? $settings->active_variant : 'a';
        $variant = $variant_map[$key];

        // Fetch tiered packages when pricing_mode is 'tiers'.
        $packages = [];
        if (!empty($settings->pricing_mode) && $settings->pricing_mode === 'tiers') {
            $this->load->model('saas_model');
            $packages = $this->saas_model->getPackages(true);
            if (!empty($packages)) {
                foreach ($packages as &$pkg) {
                    if (is_string($pkg->features)) {
                        $decoded = json_decode($pkg->features, true);
                        if ($decoded === null && $pkg->features !== '' && $pkg->features !== 'null') {
                            log_message('error', 'Landing: malformed JSON in saas_package.features for package id=' . $pkg->id);
                        }
                        $pkg->features = $decoded ?: [];
                    }
                    if (is_string($pkg->limits)) {
                        $decoded = json_decode($pkg->limits, true);
                        if ($decoded === null && $pkg->limits !== '' && $pkg->limits !== 'null') {
                            log_message('error', 'Landing: malformed JSON in saas_package.limits for package id=' . $pkg->id);
                        }
                        $pkg->limits = $decoded ?: [];
                    }
                }
                unset($pkg);
            }
        }

        $feature_labels = [
            'dashboard'      => 'Admin dashboard',
            'student'        => 'Student records',
            'staff'          => 'Staff records',
            'class'          => 'Classes & sections',
            'attendance'     => 'Attendance',
            'exam'           => 'Exam & gradebook',
            'frontend'       => 'Public school website',
            'fees'           => 'Fee collection',
            'notice'         => 'Notices',
            'sms'            => 'SMS notifications',
            'accounting'     => 'Full accounting',
            'library'        => 'Library',
            'transport'      => 'Transport',
            'hostel'         => 'Hostel',
            'custom_domain'  => 'Custom domain',
            'api'            => 'REST API',
            'parent'         => 'Parent portal',
            'student_portal' => 'Student portal',
        ];

        $data = [
            'page_title'     => 'SmartSchool.bd — Free school management software for Bangladesh',
            'signup_url'     => base_url('signup'),
            'login_url'      => base_url('authentication'),
            's'              => $settings,
            'packages'       => $packages,
            'feature_labels' => $feature_labels,
        ];
        $this->load->view('landing/' . $variant, $data);
    }
}
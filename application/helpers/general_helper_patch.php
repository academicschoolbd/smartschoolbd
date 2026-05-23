<?php
// =============================================================================
// general_helper_patch.php — SaaS helper functions for SmartSchool.bd.
//
// APPEND this file's contents to:
//   application/helpers/general_helper.php
// at the END (just before the closing PHP tag if any). Do NOT replace the
// whole file — it has 1000+ lines of unrelated Ramom helpers.
//
// These functions ride on top of the existing checkSaasLimit() / moduleIsEnabled()
// helpers and add a more flexible feature-flag layer that reads the new
// JSON `features` column on `saas_package`.
// =============================================================================

if (!function_exists('featureEnabled')) {
    /**
     * Returns TRUE if the logged-in tenant's current plan includes $feature.
     * Super-admin (role_id=1) is always allowed.
     *
     * Example:
     *   if (!featureEnabled('accounting')) show_404();
     *   if (featureEnabled('sms')) { ... SMS module body ... }
     *
     * @param string $feature Module slug, e.g. 'accounting','sms','custom_domain','api','transport'.
     */
    function featureEnabled($feature)
    {
        $ci = &get_instance();
        $role_id  = $ci->session->userdata('loggedin_role_id');
        $branchID = (int)$ci->session->userdata('loggedin_branch');
        if ($role_id == 1) return true;

        $sql = "SELECT sp.features FROM saas_subscriptions s
                  LEFT JOIN saas_package sp ON sp.id = s.package_id
                  WHERE s.school_id = " . $ci->db->escape($branchID) . " LIMIT 1";
        $row = $ci->db->query($sql)->row();
        if (!$row || !$row->features) return true; // open by default if no package

        $features = json_decode($row->features, true);
        if (!is_array($features)) return true;
        return in_array($feature, $features, true);
    }
}

if (!function_exists('saasLimitValue')) {
    /**
     * Returns the JSON `limits` value for the logged-in tenant's plan.
     *
     * Example:
     *   $maxStorage = saasLimitValue('storage_mb', 100);
     *   if (saasLimitValue('custom_domain', false)) { ... }
     */
    function saasLimitValue($key, $default = null)
    {
        $ci = &get_instance();
        $role_id  = $ci->session->userdata('loggedin_role_id');
        $branchID = (int)$ci->session->userdata('loggedin_branch');
        if ($role_id == 1) return $default;

        $sql = "SELECT sp.limits FROM saas_subscriptions s
                  LEFT JOIN saas_package sp ON sp.id = s.package_id
                  WHERE s.school_id = " . $ci->db->escape($branchID) . " LIMIT 1";
        $row = $ci->db->query($sql)->row();
        if (!$row || !$row->limits) return $default;
        $limits = json_decode($row->limits, true);
        if (!is_array($limits)) return $default;
        return array_key_exists($key, $limits) ? $limits[$key] : $default;
    }
}

if (!function_exists('subscriptionStatus')) {
    /**
     * Returns the subscription row for the logged-in tenant, or null.
     */
    function subscriptionStatus()
    {
        $ci = &get_instance();
        $branchID = (int)$ci->session->userdata('loggedin_branch');
        return $ci->db->select('s.*, sp.code AS package_code, sp.name AS package_name')
            ->from('saas_subscriptions s')
            ->join('saas_package sp', 'sp.id = s.package_id', 'left')
            ->where('s.school_id', $branchID)
            ->get()->row();
    }
}

if (!function_exists('isTenantSuspended')) {
    /**
     * Returns TRUE if the tenant is suspended or cancelled. MY_Controller
     * should call this and redirect to /subscription if so.
     */
    function isTenantSuspended()
    {
        $sub = subscriptionStatus();
        if (!$sub) return false;
        return in_array($sub->status, ['suspended', 'cancelled'], true);
    }
}

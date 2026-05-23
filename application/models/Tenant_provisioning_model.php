<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Tenant_provisioning_model
 *
 * Clones every branch-scoped `front_cms_*` row from a template tenant
 * onto a freshly-provisioned branch so the new subdomain renders
 * identical to the template site (theme, colours, logos, copyright,
 * home-page layout, menus, services list, etc.) instead of falling
 * back to an empty placeholder.
 *
 * Used by:
 *   - Saas::approve()                       — at tenant approval time
 *   - Frontend_Controller::__construct      — self-heal on first visit
 *
 * The clone is intentionally a full row-by-row copy of every table
 * whose name starts with `front_cms_` and has a `branch_id` column.
 * For the `front_cms_setting` row alone, a small set of identity
 * fields (application_title, url_alias, email, mobile_no, address,
 * copyright_text) is overridden with the new tenant's data so the
 * cloned subdomain still announces itself correctly.
 *
 * The operation is idempotent: any table already populated for the
 * new branch is left untouched, so re-running the clone (e.g. after
 * a partial failure or via the self-heal path) is safe.
 */
class Tenant_provisioning_model extends CI_Model
{
    /**
     * Identity fields on `front_cms_setting` that are overridden with
     * tenant-specific values when cloning. Every other column on the
     * template row is preserved verbatim so the new tenant inherits
     * theme, colours, logos, slogans, social links, etc. exactly.
     */
    private $identityOverrides = [
        'application_title',
        'url_alias',
        'email',
        'mobile_no',
        'address',
        'copyright_text',
    ];

    /**
     * @param int      $newBranchId       Branch the new tenant lives on.
     * @param array    $overrides         Tenant-identity overrides for the
     *                                    front_cms_setting row (keys must
     *                                    be in $this->identityOverrides;
     *                                    others are ignored).
     * @param int|null $templateBranchId  Optional explicit template branch.
     *                                    When null, pick from
     *                                    global_settings.cms_default_branch
     *                                    then the lowest cms_active=1 row.
     *
     * @return int|null  The branch_id that was used as the template, or
     *                   null when no template was available (caller is
     *                   expected to fall back to a placeholder insert).
     */
    public function cloneFrontendForTenant($newBranchId, array $overrides = [], $templateBranchId = null)
    {
        $newBranchId = (int) $newBranchId;
        if ($newBranchId <= 0) {
            return null;
        }

        $templateBranchId = $this->_pickTemplateBranch($templateBranchId, $newBranchId);
        if (!$templateBranchId) {
            return null;
        }

        $this->_cloneFrontCmsSetting($newBranchId, $templateBranchId, $overrides);

        foreach ($this->_branchScopedFrontTables() as $table) {
            if ($table === 'front_cms_setting') {
                continue;
            }
            $this->_cloneBranchScopedRows($table, $newBranchId, $templateBranchId);
        }

        return $templateBranchId;
    }

    /**
     * Wipe every branch-scoped `front_cms_*` row for $branchId and then
     * re-run cloneFrontendForTenant() from the template. Used by the
     * super-admin "Re-clone tenant" action so already-provisioned
     * tenants (whose front_cms_* tables were populated before the
     * clone helper existed, or whose template branch changed) can be
     * brought back into sync with the current template in one click.
     */
    public function recloneFrontendForTenant($branchId, array $overrides = [], $templateBranchId = null)
    {
        $branchId = (int) $branchId;
        if ($branchId <= 0) {
            return null;
        }

        foreach ($this->_branchScopedFrontTables() as $table) {
            $this->db->where('branch_id', $branchId)->delete($table);
        }

        return $this->cloneFrontendForTenant($branchId, $overrides, $templateBranchId);
    }

    /**
     * Helper for the super-admin settings UI: every branch joined with
     * its `front_cms_setting` row (when present) so the picker can
     * label which branches are valid templates and which still need
     * setup.
     */
    public function listBranchesForProvisioning()
    {
        $this->db->select('b.id, b.name, b.school_name, b.subdomain, b.slug, fcs.id AS cms_id, fcs.cms_active, fcs.application_title');
        $this->db->from('branch b');
        $this->db->join('front_cms_setting fcs', 'fcs.branch_id = b.id', 'left');
        $this->db->order_by('b.id', 'asc');
        return $this->db->get()->result();
    }

    /**
     * Returns the currently-configured template branch id (the one new
     * approvals will clone from), or 0 when unset. Reads
     * `global_settings.cms_default_branch`.
     */
    public function getConfiguredTemplateBranchId()
    {
        if (!$this->db->field_exists('cms_default_branch', 'global_settings')) {
            return 0;
        }
        $row = $this->db->select('cms_default_branch')->where('id', 1)->limit(1)->get('global_settings')->row();
        return $row ? (int) $row->cms_default_branch : 0;
    }

    /**
     * Persists the chosen template branch in
     * `global_settings.cms_default_branch`. Returns true on a successful
     * write, false when the column doesn't exist on this install.
     */
    public function setConfiguredTemplateBranchId($branchId)
    {
        if (!$this->db->field_exists('cms_default_branch', 'global_settings')) {
            return false;
        }
        $this->db->where('id', 1)->update('global_settings', array(
            'cms_default_branch' => (int) $branchId,
        ));
        return true;
    }

    /**
     * Resolve the template branch in priority order:
     *   1. caller-supplied $explicit (if it has a front_cms_setting row)
     *   2. global_settings.cms_default_branch (if it has a row)
     *   3. lowest branch_id with cms_active=1
     *   4. lowest branch_id with any front_cms_setting row
     * Always excludes $excludeBranchId so we never clone a branch onto
     * itself (which would be a no-op anyway).
     */
    private function _pickTemplateBranch($explicit, $excludeBranchId)
    {
        $excludeBranchId = (int) $excludeBranchId;

        if ($explicit) {
            $hit = $this->db
                ->where('branch_id', (int) $explicit)
                ->limit(1)
                ->count_all_results('front_cms_setting');
            if ($hit && (int) $explicit !== $excludeBranchId) {
                return (int) $explicit;
            }
        }

        if ($this->db->field_exists('cms_default_branch', 'global_settings')) {
            $g = $this->db
                ->select('cms_default_branch')
                ->where('id', 1)
                ->limit(1)
                ->get('global_settings')
                ->row();
            if ($g && (int) $g->cms_default_branch > 0 && (int) $g->cms_default_branch !== $excludeBranchId) {
                $hit = $this->db
                    ->where('branch_id', (int) $g->cms_default_branch)
                    ->limit(1)
                    ->count_all_results('front_cms_setting');
                if ($hit) {
                    return (int) $g->cms_default_branch;
                }
            }
        }

        $row = $this->db
            ->select('branch_id')
            ->where('cms_active', 1)
            ->where('branch_id !=', $excludeBranchId)
            ->order_by('branch_id', 'asc')
            ->limit(1)
            ->get('front_cms_setting')
            ->row();
        if ($row) {
            return (int) $row->branch_id;
        }

        $row = $this->db
            ->select('branch_id')
            ->where('branch_id !=', $excludeBranchId)
            ->order_by('branch_id', 'asc')
            ->limit(1)
            ->get('front_cms_setting')
            ->row();
        return $row ? (int) $row->branch_id : null;
    }

    /**
     * Clone the single front_cms_setting row, applying tenant overrides.
     * Idempotent — does nothing when a row for $newBranchId already
     * exists.
     */
    private function _cloneFrontCmsSetting($newBranchId, $templateBranchId, array $overrides)
    {
        $exists = $this->db
            ->where('branch_id', $newBranchId)
            ->limit(1)
            ->count_all_results('front_cms_setting');
        if ($exists) {
            return;
        }

        $tpl = $this->db->get_where('front_cms_setting', array('branch_id' => $templateBranchId))->row_array();
        if (empty($tpl)) {
            return;
        }

        unset($tpl['id']);
        $tpl['branch_id']  = $newBranchId;
        $tpl['cms_active'] = 1;

        foreach ($this->identityOverrides as $col) {
            if (array_key_exists($col, $overrides) && $overrides[$col] !== null && $overrides[$col] !== '') {
                $tpl[$col] = $overrides[$col];
            }
        }

        $this->db->insert('front_cms_setting', $tpl);
    }

    /**
     * Discover every front_cms_* table that carries a branch_id column.
     * We deliberately match by prefix + column existence (rather than a
     * hardcoded allowlist) so newly-added CMS tables are picked up
     * automatically without touching this model.
     */
    private function _branchScopedFrontTables()
    {
        $all = $this->db->list_tables();
        $out = [];
        foreach ($all as $t) {
            if (strpos($t, 'front_cms_') !== 0) {
                continue;
            }
            if (!$this->db->field_exists('branch_id', $t)) {
                continue;
            }
            $out[] = $t;
        }
        return $out;
    }

    /**
     * Copy every row from $table where branch_id=$templateBranchId,
     * stamped with $newBranchId. The primary key and audit columns
     * (created_at / updated_at) are dropped so the DB applies its own
     * defaults. Idempotent — skipped entirely if any row already
     * exists for $newBranchId.
     */
    private function _cloneBranchScopedRows($table, $newBranchId, $templateBranchId)
    {
        $hasRows = $this->db
            ->where('branch_id', $newBranchId)
            ->limit(1)
            ->count_all_results($table);
        if ($hasRows) {
            return;
        }

        $rows = $this->db->get_where($table, array('branch_id' => $templateBranchId))->result_array();
        if (empty($rows)) {
            return;
        }

        foreach ($rows as $row) {
            unset($row['id'], $row['created_at'], $row['updated_at']);
            $row['branch_id'] = $newBranchId;
            $this->db->insert($table, $row);
        }
    }
}

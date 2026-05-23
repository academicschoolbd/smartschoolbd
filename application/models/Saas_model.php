<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Saas_model — DB access for the SmartSchool.bd SaaS layer.
 * Tables: saas_package, saas_subscriptions, saas_pending_request, invoice, saas_payment.
 *
 * @author SmartSchool.bd
 */
class Saas_model extends CI_Model
{
    // -------------------------------------------------------------------------
    // Packages
    // -------------------------------------------------------------------------
    public function getPackages($onlyActive = false)
    {
        $this->db->order_by('sort_order', 'asc');
        if ($onlyActive) $this->db->where('is_active', 1);
        return $this->db->get('saas_package')->result();
    }

    public function getPackageById($id)
    {
        return $this->db->where('id', (int)$id)->get('saas_package')->row();
    }

    public function getPackageByCode($code)
    {
        return $this->db->where('code', $code)->get('saas_package')->row();
    }

    public function savePackage($data, $id = null)
    {
        if ($id) {
            $this->db->where('id', (int)$id)->update('saas_package', $data);
            return (int)$id;
        }
        $this->db->insert('saas_package', $data);
        return (int)$this->db->insert_id();
    }

    public function deletePackage($id)
    {
        return $this->db->where('id', (int)$id)->delete('saas_package');
    }

    // -------------------------------------------------------------------------
    // Subscriptions
    // -------------------------------------------------------------------------
    public function getSubscriptions()
    {
        $this->db->select('s.*, b.name AS branch_name, b.subdomain, sp.code AS package_code, sp.name AS package_name, sp.price_bdt');
        $this->db->from('saas_subscriptions s');
        $this->db->join('branch b',         'b.id = s.school_id', 'left');
        $this->db->join('saas_package sp',  'sp.id = s.package_id', 'left');
        $this->db->order_by('s.id', 'desc');
        return $this->db->get()->result();
    }

    public function getSubscriptionByBranch($branchId)
    {
        $this->db->select('s.*, sp.code AS package_code, sp.name AS package_name, sp.price_bdt, sp.features, sp.limits, sp.student_limit, sp.staff_limit, sp.teacher_limit, sp.parents_limit');
        $this->db->from('saas_subscriptions s');
        $this->db->join('saas_package sp', 'sp.id = s.package_id', 'left');
        $this->db->where('s.school_id', (int)$branchId);
        return $this->db->get()->row();
    }

    public function assignPackage($branchId, $packageId, $status = 'active')
    {
        $pkg = $this->getPackageById($packageId);
        if (!$pkg) return false;

        $trialEnds = null;
        $periodEnd = null;
        if ($pkg->is_default_trial && (int)$pkg->trial_days > 0) {
            $trialEnds = date('Y-m-d', strtotime('+' . (int)$pkg->trial_days . ' days'));
            $periodEnd = $trialEnds;
        } else {
            // 30-day default for monthly billing
            $months = $pkg->billing_period === 'yearly' ? 12 : 1;
            $periodEnd = date('Y-m-d', strtotime('+' . $months . ' months'));
        }

        $row = [
            'school_id'             => (int)$branchId,
            'package_id'            => (int)$packageId,
            'status'                => $status,
            'trial_ends_at'         => $trialEnds,
            'current_period_start'  => date('Y-m-d'),
            'current_period_end'    => $periodEnd,
            'expire_date'           => $periodEnd,
            'updated_at'            => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->where('school_id', (int)$branchId)->get('saas_subscriptions')->row();
        if ($existing) {
            $this->db->where('id', $existing->id)->update('saas_subscriptions', $row);
            return (int)$existing->id;
        }
        $this->db->insert('saas_subscriptions', $row);
        return (int)$this->db->insert_id();
    }

    public function setStatus($branchId, $status)
    {
        return $this->db->where('school_id', (int)$branchId)
            ->update('saas_subscriptions', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function extendPeriod($branchId, $days)
    {
        $sub = $this->db->where('school_id', (int)$branchId)->get('saas_subscriptions')->row();
        if (!$sub) return false;
        $base = $sub->current_period_end ?: date('Y-m-d');
        $new  = date('Y-m-d', strtotime($base . ' +' . (int)$days . ' days'));
        return $this->db->where('school_id', (int)$branchId)->update('saas_subscriptions', [
            'current_period_end' => $new,
            'expire_date'        => $new,
            'status'             => 'active',
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Pending signup requests
    // -------------------------------------------------------------------------
    public function getPendingRequests($status = null)
    {
        $this->db->select('pr.*, sp.code AS package_code, sp.name AS package_name');
        $this->db->from('saas_pending_request pr');
        $this->db->join('saas_package sp', 'sp.id = pr.package_id', 'left');
        if ($status) $this->db->where('pr.status', $status);
        $this->db->order_by('pr.id', 'desc');
        return $this->db->get()->result();
    }

    public function getPendingRequestById($id)
    {
        return $this->db->where('id', (int)$id)->get('saas_pending_request')->row();
    }

    public function savePendingRequest($data)
    {
        $this->db->insert('saas_pending_request', $data);
        return (int)$this->db->insert_id();
    }

    public function markRequestProcessed($id, $status, $branchId = null)
    {
        $upd = [
            'status'       => $status,
            'processed_at' => date('Y-m-d H:i:s'),
        ];
        if ($branchId) $upd['branch_id'] = (int)$branchId;
        return $this->db->where('id', (int)$id)->update('saas_pending_request', $upd);
    }

    public function isSubdomainTaken($subdomain)
    {
        $reserved = ['www','admin','api','app','mail','signup','test','dev','staging','support','help','docs','blog'];
        $sd = strtolower(trim($subdomain));
        if (in_array($sd, $reserved, true)) return true;
        $existsInBranch  = $this->db->where('subdomain', $sd)->count_all_results('branch') > 0;
        $existsInPending = $this->db->where('subdomain', $sd)->where('status', 'pending')->count_all_results('saas_pending_request') > 0;
        return $existsInBranch || $existsInPending;
    }

    // -------------------------------------------------------------------------
    // Invoices & payments
    // -------------------------------------------------------------------------
    public function getInvoicesForBranch($branchId)
    {
        return $this->db->where('branch_id', (int)$branchId)
            ->order_by('id', 'desc')
            ->get('invoice')->result();
    }

    public function getAllInvoices()
    {
        $this->db->select('i.*, b.name AS branch_name, b.subdomain');
        $this->db->from('invoice i');
        $this->db->join('branch b', 'b.id = i.branch_id', 'left');
        $this->db->order_by('i.id', 'desc');
        return $this->db->get()->result();
    }

    public function getInvoiceById($id)
    {
        return $this->db->where('id', (int)$id)->get('invoice')->row();
    }

    public function createInvoice($branchId, $subscriptionId, $amount, $periodStart, $periodEnd, $dueDate = null)
    {
        $no = 'INV-' . date('Ymd') . '-' . str_pad((string)random_int(100, 9999), 4, '0', STR_PAD_LEFT);
        $row = [
            'branch_id'        => (int)$branchId,
            'subscription_id'  => (int)$subscriptionId,
            'invoice_no'       => $no,
            'period_start'     => $periodStart,
            'period_end'       => $periodEnd,
            'amount'           => $amount,
            'currency'         => 'BDT',
            'status'           => 'open',
            'due_date'         => $dueDate ?: date('Y-m-d', strtotime('+7 days')),
        ];
        $this->db->insert('invoice', $row);
        return (int)$this->db->insert_id();
    }

    public function markInvoicePaid($invoiceId, $provider = 'manual', $providerTxnId = null, $raw = null)
    {
        $inv = $this->getInvoiceById($invoiceId);
        if (!$inv || $inv->status === 'paid') return false;

        $now = date('Y-m-d H:i:s');
        $this->db->where('id', (int)$invoiceId)->update('invoice', [
            'status'  => 'paid',
            'paid_at' => $now,
        ]);
        $this->db->insert('saas_payment', [
            'invoice_id'      => (int)$invoiceId,
            'branch_id'       => (int)$inv->branch_id,
            'amount'          => $inv->amount,
            'currency'        => $inv->currency,
            'provider'        => $provider,
            'provider_txn_id' => $providerTxnId,
            'status'          => 'succeeded',
            'paid_at'         => $now,
            'raw_response'    => $raw ? (is_string($raw) ? $raw : json_encode($raw)) : null,
        ]);
        // Extend the subscription by one period
        $sub = $this->db->where('id', (int)$inv->subscription_id)->get('saas_subscriptions')->row();
        if ($sub) {
            $pkg = $this->getPackageById($sub->package_id);
            $days = ($pkg && $pkg->billing_period === 'yearly') ? 365 : 30;
            $this->extendPeriod($sub->school_id, $days);
        }
        return true;
    }

    public function getPaymentsForBranch($branchId)
    {
        return $this->db->where('branch_id', (int)$branchId)
            ->order_by('id', 'desc')
            ->get('saas_payment')->result();
    }

    public function getAllPayments()
    {
        $this->db->select('p.*, b.name AS branch_name, b.subdomain, i.invoice_no');
        $this->db->from('saas_payment p');
        $this->db->join('branch b',  'b.id = p.branch_id', 'left');
        $this->db->join('invoice i', 'i.id = p.invoice_id', 'left');
        $this->db->order_by('p.id', 'desc');
        return $this->db->get()->result();
    }

    // -------------------------------------------------------------------------
    // Usage stats per branch (for the dashboard widget)
    // -------------------------------------------------------------------------
    public function getUsageStats($branchId)
    {
        // `student` has no branch_id column; per-branch scope lives on
        // `enroll`. Count distinct active (non-alumni) students for this
        // branch via an explicit COUNT(DISTINCT student_id) — CI's QB
        // `distinct()` + `count_all_results()` combo emits a subquery that
        // some MariaDB versions reject and surfaces as a fatal in
        // count_all_results (see DB_query_builder line 1430).
        $branchId = (int) $branchId;

        $row = $this->db
            ->select('COUNT(DISTINCT student_id) AS cnt', false)
            ->where('branch_id', $branchId)
            ->where('is_alumni', 0)
            ->get('enroll')
            ->row();
        $studentCount = $row ? (int) $row->cnt : 0;

        $staffCount = (int) $this->db
            ->where('branch_id', $branchId)
            ->count_all_results('staff');

        return ['students' => $studentCount, 'staff' => $staffCount];
    }
}
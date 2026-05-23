<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Saas_manual_payment_submission_model — CRUD over saas_manual_payment_submission.
 *
 * Rows represent a tenant's submitted proof of a bank/MFS transfer for a SaaS
 * invoice. Super-admin approves => triggers Saas_model::markInvoicePaid().
 *
 * @author SmartSchool.bd
 */
class Saas_manual_payment_submission_model extends CI_Model
{
    protected $table = 'saas_manual_payment_submission';

    public function create(array $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return (int)$this->db->insert_id();
    }

    public function getById($id)
    {
        return $this->db->where('id', (int)$id)->get($this->table)->row();
    }

    /** Pending submissions across all branches (super-admin queue). */
    public function getPending()
    {
        return $this->db
            ->select('m.*, b.name AS branch_name, b.slug AS branch_slug, i.invoice_no, i.amount AS invoice_amount, i.status AS invoice_status')
            ->from($this->table . ' m')
            ->join('branch b', 'b.id = m.branch_id', 'left')
            ->join('invoice i', 'i.id = m.invoice_id', 'left')
            ->where('m.status', 'pending')
            ->order_by('m.created_at', 'ASC')
            ->get()->result();
    }

    /** All submissions across all branches, newest first. */
    public function getAll($limit = 200)
    {
        return $this->db
            ->select('m.*, b.name AS branch_name, i.invoice_no, i.amount AS invoice_amount, i.status AS invoice_status')
            ->from($this->table . ' m')
            ->join('branch b', 'b.id = m.branch_id', 'left')
            ->join('invoice i', 'i.id = m.invoice_id', 'left')
            ->order_by('m.created_at', 'DESC')
            ->limit($limit)
            ->get()->result();
    }

    /** Submissions for a single branch (tenant-facing). */
    public function getForBranch($branchId, $limit = 50)
    {
        return $this->db
            ->select('m.*, i.invoice_no, i.amount AS invoice_amount, i.status AS invoice_status')
            ->from($this->table . ' m')
            ->join('invoice i', 'i.id = m.invoice_id', 'left')
            ->where('m.branch_id', (int)$branchId)
            ->order_by('m.created_at', 'DESC')
            ->limit($limit)
            ->get()->result();
    }

    /** True if this invoice already has a pending submission (anti-spam). */
    public function hasPendingForInvoice($invoiceId)
    {
        return (int)$this->db->where(['invoice_id' => (int)$invoiceId, 'status' => 'pending'])
            ->count_all_results($this->table) > 0;
    }

    public function setStatus($id, $status, $reviewerId, $notes = null)
    {
        return $this->db->where('id', (int)$id)->update($this->table, [
            'status'       => $status,
            'reviewed_by'  => (int)$reviewerId,
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'review_notes' => $notes,
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}

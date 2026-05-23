<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Saas_payment_gateway_model — CRUD for the `saas_payment_gateway` table.
 *
 * Rows are seeded by the 2026-05-22 migration (one per provider code).
 * The super-admin UI at /saas/payment_gateways uses this model to toggle
 * enabled/sandbox flags and persist per-provider credentials (stored as
 * JSON in `credentials_json`).
 *
 * Provider codes recognised by the runtime: manual, sslcommerz, stripe,
 * bkash, nagad, rocket, paykureghor.
 *
 * @author SmartSchool.bd
 */
class Saas_payment_gateway_model extends CI_Model
{
    public function getAll($onlyEnabled = false)
    {
        $this->db->order_by('sort_order', 'asc');
        if ($onlyEnabled) $this->db->where('is_enabled', 1);
        return $this->db->get('saas_payment_gateway')->result();
    }

    public function getById($id)
    {
        return $this->db->where('id', (int)$id)->get('saas_payment_gateway')->row();
    }

    public function getByCode($code)
    {
        return $this->db->where('code', $code)->get('saas_payment_gateway')->row();
    }

    /**
     * Decoded credentials hash for a given provider code. Returns an empty
     * array if the row is missing, the credentials column is NULL, or the
     * JSON fails to parse.
     */
    public function getCredentials($code)
    {
        $row = $this->getByCode($code);
        if (!$row || empty($row->credentials_json)) return [];
        $decoded = json_decode($row->credentials_json, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function save($id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', (int)$id)->update('saas_payment_gateway', $data);
    }

    public function saveCredentials($code, array $credentials)
    {
        $row = $this->getByCode($code);
        if (!$row) return false;
        return $this->db->where('id', (int)$row->id)->update('saas_payment_gateway', [
            'credentials_json' => json_encode($credentials, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    public function setEnabled($id, $enabled)
    {
        return $this->db->where('id', (int)$id)->update('saas_payment_gateway', [
            'is_enabled' => $enabled ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

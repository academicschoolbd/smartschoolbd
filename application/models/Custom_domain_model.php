<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Custom_domain_model — CRUD + DNS probe for the `custom_domain` table.
 *
 * Columns (per 002_custom_domain_and_saas.sql):
 *   id, school_id, url, domain_type ENUM('subdomain','custom'), status TINYINT,
 *   notes, created_at, updated_at.
 *
 * @author SmartSchool.bd
 */
class Custom_domain_model extends CI_Model
{
    public function getAll()
    {
        $this->db->select('cd.*, b.name AS branch_name, b.subdomain AS branch_subdomain');
        $this->db->from('custom_domain cd');
        $this->db->join('branch b', 'b.id = cd.school_id', 'left');
        $this->db->order_by('cd.id', 'desc');
        return $this->db->get()->result();
    }

    public function getByBranch($branchId)
    {
        return $this->db->where('school_id', (int)$branchId)
            ->order_by('id', 'desc')
            ->get('custom_domain')->result();
    }

    public function getById($id)
    {
        return $this->db->where('id', (int)$id)->get('custom_domain')->row();
    }

    public function getByUrl($url)
    {
        return $this->db->where('url', $url)->get('custom_domain')->row();
    }

    public function insert($data)
    {
        if (!isset($data['created_at'])) $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('custom_domain', $data);
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', (int)$id)->update('custom_domain', $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', (int)$id)->delete('custom_domain');
    }

    public function toggleStatus($id)
    {
        $row = $this->getById($id);
        if (!$row) return false;
        $new = ((int)$row->status === 1) ? 0 : 1;
        return $this->update($id, ['status' => $new]);
    }

    /**
     * Probe DNS for the configured host.
     *  - subdomain: always trivially OK (wildcard A is in DNS).
     *  - custom: must resolve via A or CNAME to a host that ends in smartschool.bd
     *            (or to one of the apex A records).
     *
     * Returns ['ok' => bool, 'detail' => string].
     */
    public function probe($host)
    {
        $host = strtolower(trim((string)$host));
        if ($host === '') {
            return ['ok' => false, 'detail' => 'empty host'];
        }

        // Subdomain of smartschool.bd → trusted (covered by wildcard A)
        if (preg_match('/\.smartschool\.bd$/', $host)) {
            return ['ok' => true, 'detail' => 'subdomain of smartschool.bd'];
        }

        if (!function_exists('dns_get_record')) {
            return ['ok' => false, 'detail' => 'dns_get_record() not available'];
        }
        $cnames = @dns_get_record($host, DNS_CNAME) ?: [];
        foreach ($cnames as $r) {
            $target = strtolower((string)($r['target'] ?? ''));
            if (preg_match('/(^|\.)smartschool\.bd\.?$/', $target)) {
                return ['ok' => true, 'detail' => 'CNAME → ' . $target];
            }
        }
        $a = @dns_get_record($host, DNS_A) ?: [];
        $apex = @dns_get_record('smartschool.bd', DNS_A) ?: [];
        $apexIps = array_map(static fn($r) => $r['ip'] ?? '', $apex);
        foreach ($a as $r) {
            if (!empty($r['ip']) && in_array($r['ip'], $apexIps, true)) {
                return ['ok' => true, 'detail' => 'A → ' . $r['ip'] . ' (matches apex)'];
            }
        }
        return ['ok' => false, 'detail' => 'no matching A/CNAME found'];
    }
}

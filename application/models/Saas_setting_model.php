<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Saas_setting_model — tiny key/value store backing the SaaS billing settings
 * UI on /saas/billing_settings.
 *
 * Keys in use today (seeded by the 2026-05-22c migration):
 *   renewal_grace_days        — int (days)
 *   renewal_email_subject     — string
 *   renewal_email_body        — html template, supports {placeholders}
 *   renewal_email_from_name   — string
 *   renewal_email_from_email  — string (email address)
 *   billing_contact_email     — string (email address)
 *
 * @author SmartSchool.bd
 */
class Saas_setting_model extends CI_Model
{
    protected $table = 'saas_setting';

    /** @return string|null Raw value, or $default if the key is missing. */
    public function get($key, $default = null)
    {
        $row = $this->db->where('setting_key', $key)->get($this->table)->row();
        if (!$row) return $default;
        return $row->setting_value;
    }

    /** Integer accessor with default + bounds. */
    public function getInt($key, $default = 0, $min = null, $max = null)
    {
        $val = $this->get($key, null);
        if ($val === null || $val === '') return $default;
        $i = (int)$val;
        if ($min !== null && $i < $min) $i = $min;
        if ($max !== null && $i > $max) $i = $max;
        return $i;
    }

    /** All keys at once (for the settings form). */
    public function getAllAsMap()
    {
        $rows = $this->db->get($this->table)->result();
        $map = [];
        foreach ($rows as $r) $map[$r->setting_key] = $r->setting_value;
        return $map;
    }

    /** Upsert one key/value pair. */
    public function set($key, $value)
    {
        $exists = $this->db->where('setting_key', $key)->count_all_results($this->table) > 0;
        if ($exists) {
            return $this->db->where('setting_key', $key)->update($this->table, [
                'setting_value' => $value,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
        return $this->db->insert($this->table, [
            'setting_key'   => $key,
            'setting_value' => $value,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function setMany(array $kv)
    {
        foreach ($kv as $k => $v) $this->set($k, $v);
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Saas_notification_channel_model — read/write the saas_notification_channel
 * table. Today only the 'telegram' row is used. The schema is intentionally
 * generic so future channels (Slack/Discord/SMS/webhook) can be added without
 * a migration.
 *
 * @author SmartSchool.bd
 */
class Saas_notification_channel_model extends CI_Model
{
    protected $table = 'saas_notification_channel';

    public function getByCode($code)
    {
        return $this->db->where('code', $code)->get($this->table)->row();
    }

    public function getConfig($code)
    {
        $row = $this->getByCode($code);
        if (!$row || !$row->config_json) return [];
        $cfg = json_decode($row->config_json, true);
        return is_array($cfg) ? $cfg : [];
    }

    public function isEnabled($code)
    {
        $row = $this->getByCode($code);
        return $row && (int)$row->is_enabled === 1;
    }

    public function save($code, array $config, $isEnabled)
    {
        $existing = $this->getByCode($code);
        $json = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($existing) {
            return $this->db->where('id', (int)$existing->id)->update($this->table, [
                'is_enabled'  => $isEnabled ? 1 : 0,
                'config_json' => $json,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }
        return $this->db->insert($this->table, [
            'code'        => $code,
            'name'        => ucfirst($code),
            'is_enabled'  => $isEnabled ? 1 : 0,
            'config_json' => $json,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}

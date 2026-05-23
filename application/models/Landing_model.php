<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Landing_model — load/save the singleton `landing_setting` row.
 *
 * Always returns a fully-populated settings object, falling back to the
 * baked-in defaults if the table is missing (so the public landing page
 * never breaks because the SQL migration wasn't run yet).
 *
 * @author SmartSchool.bd
 */
class Landing_model extends CI_Model
{
    /** Hard-coded defaults — mirror the SQL migration defaults exactly. */
    private const DEFAULTS = [
        'id'                  => 1,
        'active_variant'      => 'a',
        'brand_color'         => '#1f9d55',
        'hero_h1'             => 'Run your school in 5 minutes — on us.',
        'hero_bn'             => 'আপনার স্কুলের জন্য সম্পূর্ণ ফ্রি স্কুল ম্যানেজমেন্ট — কোনো কার্ড লাগবে না।',
        'hero_lead'           => 'Admissions, attendance, exams, fees, accounting, parent SMS and your own public school website — all in Bengali and English, on your own schoolname.smartschool.bd. Every feature, for every school, free for now.',
        'hero_eyebrow'        => 'Free for every Bangladeshi school — no card, no limits',
        'cta_primary_label'   => 'Sign your school up — free',
        'cta_secondary_label' => 'See what is included',
        'pricing_mode'        => 'free',
        'pricing_headline'    => 'One plan. Everything included.',
        'pricing_future_note' => 'In a few months — once we have more schools onboard and server costs start adding up — we will introduce optional paid plans for the heavier features (custom domain, REST API, large storage). Anything you create today stays on the free plan; you will never be forced to pay to keep your existing data running.',
        'show_features'       => 1,
        'show_pricing'        => 1,
        'show_testimonials'   => 1,
        'show_schools'        => 1,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /** Returns the settings as a stdClass — never null. */
    public function get(): object
    {
        if ($this->db->table_exists('landing_setting')) {
            $row = $this->db->get_where('landing_setting', ['id' => 1])->row_array();
            if (is_array($row) && !empty($row)) {
                // Backfill any column missing from the row (older DB snapshot).
                return (object)array_merge(self::DEFAULTS, array_filter($row, function($v){ return $v !== null; }));
            }
        }
        return (object)self::DEFAULTS;
    }

    /**
     * Save the supplied fields.  Allowed fields are whitelisted; anything
     * else from $_POST is dropped.  Returns true on success.
     */
    public function save(array $input, ?int $updated_by = null): bool
    {
        if (!$this->db->table_exists('landing_setting')) return false;

        $allowed = array_keys(self::DEFAULTS);
        $data = [];
        foreach ($allowed as $col) {
            if ($col === 'id') continue;
            if (!array_key_exists($col, $input)) continue;
            $v = $input[$col];
            // Coerce the tinyint(1) flags.
            if (in_array($col, ['show_features','show_pricing','show_testimonials','show_schools'], true)) {
                $v = (int)(!empty($v));
            }
            // Trim strings.
            if (is_string($v)) $v = trim($v);
            $data[$col] = $v;
        }

        // Defensive normalisation.
        if (isset($data['active_variant']) && !in_array($data['active_variant'], ['a','b','c','d','e'], true)) {
            $data['active_variant'] = 'a';
        }
        if (isset($data['pricing_mode']) && !in_array($data['pricing_mode'], ['free','tiers','hidden'], true)) {
            $data['pricing_mode'] = 'free';
        }
        if (isset($data['brand_color']) && !preg_match('/^#[0-9a-fA-F]{3,8}$/', (string)$data['brand_color'])) {
            $data['brand_color'] = '#1f9d55';
        }

        if (empty($data)) return false;
        if ($updated_by !== null) $data['updated_by'] = $updated_by;

        // Singleton upsert: row id=1 must exist after the migration; if not, insert.
        $exists = $this->db->where('id', 1)->count_all_results('landing_setting') > 0;
        if ($exists) {
            $this->db->where('id', 1)->update('landing_setting', $data);
        } else {
            $data['id'] = 1;
            $this->db->insert('landing_setting', $data);
        }
        return $this->db->affected_rows() >= 0;
    }
}
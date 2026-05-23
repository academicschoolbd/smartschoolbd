<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/Saas_gateways/Saas_gateway_interface.php';
require_once APPPATH . 'libraries/Saas_gateways/Saas_gateway_base.php';

/**
 * Saas_gateway — central registry / factory for SaaS billing adapters.
 *
 * Usage:
 *   $this->load->library('saas_gateway');
 *   $codes  = $this->saas_gateway->enabled_codes();
 *   $impl   = $this->saas_gateway->get('sslcommerz');
 *   $result = $impl->initiate($invoice, $branch);
 *
 * The class itself is a thin wrapper around Saas_payment_gateway_model:
 * it reads the row by code, lazy-loads the adapter file, hydrates the
 * adapter with the decoded credentials JSON, and returns the instance.
 *
 * @author SmartSchool.bd
 */
class Saas_gateway
{
    /** Codes the runtime knows how to instantiate. Order is display order. */
    public static $known_codes = [
        'manual', 'sslcommerz', 'stripe', 'bkash', 'nagad', 'rocket', 'paykureghor',
    ];

    /**
     * Mapping of code → [adapter file, adapter class].
     * Adapter files live in application/libraries/Saas_gateways/.
     */
    protected static $adapter_map = [
        'manual'      => ['Saas_manual_gateway.php',      'Saas_manual_gateway'],
        'sslcommerz'  => ['Saas_sslcommerz_gateway.php',  'Saas_sslcommerz_gateway'],
        'stripe'      => ['Saas_stripe_gateway.php',      'Saas_stripe_gateway'],
        'bkash'       => ['Saas_bkash_gateway.php',       'Saas_bkash_gateway'],
        'nagad'       => ['Saas_nagad_gateway.php',       'Saas_nagad_gateway'],
        'rocket'      => ['Saas_rocket_gateway.php',      'Saas_rocket_gateway'],
        'paykureghor' => ['Saas_paykureghor_gateway.php', 'Saas_paykureghor_gateway'],
    ];

    /** @var CI_Controller */
    protected $ci;
    /** @var Saas_gateway_base[] keyed by code */
    protected $cache = [];

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('saas_payment_gateway_model');
    }

    /**
     * Returns the rows of `saas_payment_gateway` for the codes the runtime
     * knows how to instantiate, in display order.
     *
     * @return object[]
     */
    public function gateway_rows($onlyEnabled = false)
    {
        $rows = $this->ci->saas_payment_gateway_model->getAll($onlyEnabled);
        $byCode = [];
        foreach ($rows as $r) $byCode[$r->code] = $r;
        $ordered = [];
        foreach (self::$known_codes as $code) {
            if (isset($byCode[$code])) $ordered[] = $byCode[$code];
        }
        return $ordered;
    }

    /** Provider codes currently enabled AND known to the runtime. */
    public function enabled_codes()
    {
        $codes = [];
        foreach ($this->gateway_rows(true) as $r) $codes[] = $r->code;
        return $codes;
    }

    /**
     * Instantiate the adapter for $code. Returns null if the code is not
     * known to the runtime, or if no row exists yet (migration not run).
     *
     * @return Saas_gateway_base|null
     */
    public function get($code)
    {
        $code = strtolower((string)$code);
        if (isset($this->cache[$code])) return $this->cache[$code];
        if (!isset(self::$adapter_map[$code])) return null;

        $row = $this->ci->saas_payment_gateway_model->getByCode($code);
        if (!$row) return null;

        list($file, $class) = self::$adapter_map[$code];
        $path = APPPATH . 'libraries/Saas_gateways/' . $file;
        if (!is_file($path)) return null;
        require_once $path;
        if (!class_exists($class)) return null;

        $creds = [];
        if (!empty($row->credentials_json)) {
            $decoded = json_decode($row->credentials_json, true);
            if (is_array($decoded)) $creds = $decoded;
        }
        $instance = new $class($creds, (bool)$row->is_sandbox);
        $this->cache[$code] = $instance;
        return $instance;
    }
}

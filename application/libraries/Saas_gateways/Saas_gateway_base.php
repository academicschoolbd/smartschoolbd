<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once __DIR__ . '/Saas_gateway_interface.php';

/**
 * Saas_gateway_base — sensible defaults every adapter inherits.
 *
 * Subclasses set $code, $name, $supports_recurring, $required_credentials
 * and override the lifecycle methods (initiate, handle_callback, handle_ipn).
 */
abstract class Saas_gateway_base implements Saas_gateway_interface
{
    /** @var string */
    protected $code = '';
    /** @var string */
    protected $name = '';
    /** @var bool */
    protected $supports_recurring = false;
    /** @var bool */
    protected $is_sandbox = true;
    /** @var array */
    protected $credentials = [];
    /** @var string[] */
    protected $required_credentials = [];
    /** @var CI_Controller */
    protected $ci;

    public function __construct($credentials = [], $is_sandbox = true)
    {
        $this->ci          = &get_instance();
        $this->credentials = is_array($credentials) ? $credentials : [];
        $this->is_sandbox  = (bool)$is_sandbox;
    }

    public function code()               { return $this->code; }
    public function display_name()       { return $this->name; }
    public function supports_recurring() { return (bool)$this->supports_recurring; }

    public function is_configured()
    {
        foreach ($this->required_credentials as $key) {
            if (empty($this->credentials[$key])) return false;
        }
        return true;
    }

    /**
     * Default initiate: error out. Adapters that don't override this are
     * not implemented yet.
     */
    public function initiate($invoice, $branch)
    {
        return [
            'action'  => 'error',
            'message' => sprintf('Provider "%s" is not implemented yet.', $this->code),
        ];
    }

    /**
     * Default callback: treat as failure. Adapters override to validate
     * the signed payload from the gateway.
     */
    public function handle_callback($invoiceId, array $payload)
    {
        return ['status' => 'failed', 'txn_id' => null, 'raw' => $payload];
    }

    /** Default IPN: treat as failure. */
    public function handle_ipn($invoiceId, array $payload)
    {
        return ['status' => 'failed', 'txn_id' => null, 'raw' => $payload];
    }

    // -------------------------------------------------------------------------
    // Helpers shared by adapters
    // -------------------------------------------------------------------------

    /**
     * POST to a URL and return [http_status, body, curl_error].
     */
    protected function http_post($url, $data, array $headers = [], $asJson = false)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($asJson) {
            $body = is_string($data) ? $data : json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            $headers = array_merge(['Content-Type: application/json', 'Accept: application/json'], $headers);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        return [$code, $body, $err];
    }

    /** GET a URL and return [http_status, body, curl_error]. */
    protected function http_get($url, array $headers = [])
    {
        $ch = curl_init($url);
        if (!empty($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
        return [$code, $body, $err];
    }

    protected function callback_url($action)
    {
        // /billing/<action>/<provider>
        return base_url('billing/' . rawurlencode($action) . '/' . rawurlencode($this->code));
    }
}

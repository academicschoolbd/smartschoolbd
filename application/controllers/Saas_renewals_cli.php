<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/Saas_renewal_runner.php';

/**
 * Saas_renewals_cli — auto-renewal cron, invoked from the CodeIgniter CLI.
 *
 * The actual work lives in Saas_renewal_runner so the same code path can be
 * triggered from the super-admin web UI (Saas::run_renewal_cron_now). This
 * controller is the cPanel cron entrypoint and nothing else.
 *
 * Usage (cPanel cron, once a day at 02:00):
 *
 *   /opt/cpanel/ea-php82/root/usr/bin/php-cli \
 *     /home/zgruhjabaz/smartschool.bd/index.php \
 *     saas_renewals_cli run >> /home/zgruhjabaz/logs/saas-renew.log 2>&1
 *
 * The script is idempotent — re-running it on the same day creates zero new
 * invoices because the runner filters out subscriptions that already have an
 * open invoice covering the upcoming period.
 *
 * @author SmartSchool.bd
 */
class Saas_renewals_cli extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('saas_model');
        $this->load->model('saas_setting_model');
        $this->load->helper(['url', 'saas_notify']);
    }

    public function run()
    {
        if (!is_cli()) {
            show_error('CLI only.', 403);
            return;
        }
        $runner = new Saas_renewal_runner($this);
        $stats  = $runner->run();
        fwrite(STDOUT, sprintf(
            "[%s] saas_renewals_cli: invoices_created=%d emails_sent=%d skipped=%d\n",
            date('c'), $stats['created'], $stats['emailed'], $stats['skipped']
        ));
        saas_notify_renewal_cron($stats['created'], $stats['emailed'], $stats['skipped']);
    }
}

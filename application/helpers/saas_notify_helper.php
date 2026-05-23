<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * saas_notify_helper — out-of-band notifications for SaaS billing.
 *
 * Currently exposes notify_admin_telegram(). Best-effort: failures are
 * swallowed and never block the calling request. All channels read their
 * config from saas_notification_channel.
 *
 * Usage:
 *   $this->load->helper('saas_notify');
 *   notify_admin_telegram("Invoice #" . $inv->id . " paid via " . $provider);
 */

if (!function_exists('notify_admin_telegram')) {
    /**
     * Send a Markdown-formatted message to the configured admin Telegram chat.
     *
     * @param string $message  Telegram-MarkdownV2 not used — we send plain text
     *                         to avoid escaping headaches. Emoji are OK.
     * @return bool            True on 2xx from Telegram, false otherwise.
     */
    function notify_admin_telegram($message)
    {
        $CI = &get_instance();
        $CI->load->model('saas_notification_channel_model');
        if (!$CI->saas_notification_channel_model->isEnabled('telegram')) {
            return false;
        }
        $cfg = $CI->saas_notification_channel_model->getConfig('telegram');
        $token   = trim($cfg['bot_token']      ?? '');
        $chatId  = trim($cfg['admin_chat_id']  ?? '');
        if ($token === '' || $chatId === '') return false;

        $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
        $body = http_build_query([
            'chat_id' => $chatId,
            'text'    => mb_substr($message, 0, 3900),
            // No parse_mode: Telegram accepts plain text with emoji.
            'disable_web_page_preview' => 'true',
        ]);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_TIMEOUT        => 6,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code >= 200 && $code < 300;
    }
}

if (!function_exists('saas_notify_payment_paid')) {
    /** Convenience wrapper: notify admin when an invoice is marked paid. */
    function saas_notify_payment_paid($invoice, $branch, $provider, $txnId = null)
    {
        $msg  = "💰 SaaS payment received\n";
        $msg .= "Invoice: " . ($invoice->invoice_no ?? ('#' . $invoice->id)) . "\n";
        $msg .= "Amount: ৳" . number_format((float)$invoice->amount, 2) . "\n";
        $msg .= "School: " . ($branch->name ?? 'unknown') . "\n";
        $msg .= "Provider: " . $provider;
        if ($txnId) $msg .= "\nTxn: " . $txnId;
        return notify_admin_telegram($msg);
    }
}

if (!function_exists('saas_notify_manual_pending')) {
    /** Notify admin when a tenant submits a manual-payment proof for review. */
    function saas_notify_manual_pending($submission, $invoice, $branch)
    {
        $msg  = "🧾 Manual SaaS payment pending review\n";
        $msg .= "Invoice: " . ($invoice->invoice_no ?? ('#' . $invoice->id)) . "\n";
        $msg .= "Amount: ৳" . number_format((float)$invoice->amount, 2) . "\n";
        $msg .= "School: " . ($branch->name ?? 'unknown') . "\n";
        $msg .= "Submitted ref: " . $submission->txn_ref . "\n";
        if (!empty($submission->payer_name))  $msg .= "Payer: " . $submission->payer_name . "\n";
        if (!empty($submission->payer_phone)) $msg .= "Phone: " . $submission->payer_phone . "\n";
        $msg .= "Review: " . base_url('saas/manual_payments');
        return notify_admin_telegram($msg);
    }
}

if (!function_exists('saas_notify_renewal_cron')) {
    /** Notify admin after the daily renewal cron finishes. */
    function saas_notify_renewal_cron($created, $emailed, $skipped)
    {
        if ($created === 0 && $emailed === 0) return false;
        $msg  = "📨 SaaS auto-renewal cron\n";
        $msg .= "Invoices created: " . (int)$created . "\n";
        $msg .= "Admin emails sent: " . (int)$emailed . "\n";
        $msg .= "Skipped: " . (int)$skipped;
        return notify_admin_telegram($msg);
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');
$title = 'Pay invoice ' . html_escape($invoice->invoice_no);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=$title?> &middot; SmartSchool.bd</title>
<link rel="stylesheet" href="<?=base_url('assets/global/css/bootstrap.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/global/css/fontawesome.css');?>">
<style>
body{background:#f7f7f9;font-family:'Open Sans',sans-serif;padding:40px 0}
.pay-card{max-width:640px;margin:0 auto;background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.08);overflow:hidden}
.pay-head{padding:20px 24px;border-bottom:1px solid #eee}
.pay-head h2{margin:0;font-size:1.25rem}
.pay-body{padding:24px}
.pay-meta{margin-bottom:24px;color:#666}
.pay-meta strong{color:#222}
.provider-list{list-style:none;padding:0;margin:0}
.provider-list li{margin:0 0 10px}
.provider-list button{width:100%;text-align:left;padding:12px 16px;border:1px solid #ddd;background:#fff;border-radius:6px;cursor:pointer;font-size:1rem}
.provider-list button:hover{border-color:#1f9d55;background:#f4fdf7}
.no-providers{padding:16px;background:#fff3cd;border:1px solid #ffeeba;color:#856404;border-radius:6px}
</style>
</head>
<body>
<div class="pay-card">
  <div class="pay-head">
    <h2>Pay invoice <code><?=html_escape($invoice->invoice_no);?></code></h2>
  </div>
  <div class="pay-body">
    <div class="pay-meta">
      <div><strong>School:</strong> <?=html_escape($branch->name ?? '');?></div>
      <div><strong>Amount:</strong> <?=html_escape($invoice->currency);?> <?=number_format((float)$invoice->amount, 2);?></div>
      <?php if (!empty($invoice->due_date)): ?>
        <div><strong>Due:</strong> <?=html_escape($invoice->due_date);?></div>
      <?php endif; ?>
      <div><strong>Status:</strong> <?=html_escape($invoice->status);?></div>
    </div>

    <?php if ($invoice->status === 'paid'): ?>
      <div class="alert alert-success">This invoice is already paid.</div>
    <?php elseif (empty($providers)): ?>
      <div class="no-providers">
        No payment providers are enabled and configured yet. Please ask the
        super-admin to enable a provider in /saas/payment_gateways.
      </div>
    <?php else: ?>
      <p>Choose a payment method:</p>
      <ul class="provider-list">
        <?php foreach ($providers as $p): ?>
        <li>
          <form method="POST" action="<?=base_url('billing/start/' . (int)$invoice->id . '/' . rawurlencode($p['code']));?>">
            <button type="submit"><i class="fas fa-credit-card"></i>&nbsp; <?=html_escape($p['name']);?></button>
          </form>
        </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

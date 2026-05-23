<?php
defined('BASEPATH') or exit('No direct script access allowed');
$ok = !empty($success);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Payment <?=$ok ? 'received' : 'status'?> &middot; SmartSchool.bd</title>
<link rel="stylesheet" href="<?=base_url('assets/global/css/bootstrap.css');?>">
<style>
body{background:#f7f7f9;font-family:'Open Sans',sans-serif;padding:40px 0;text-align:center}
.result-card{max-width:520px;margin:0 auto;background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:32px}
.result-icon{font-size:48px;margin-bottom:12px}
.result-icon.ok{color:#1f9d55}
.result-icon.bad{color:#dc3545}
.result-msg{margin:12px 0 20px;color:#444;text-align:left}
.btn-back{display:inline-block;padding:10px 20px;background:#1f9d55;color:#fff;text-decoration:none;border-radius:4px}
</style>
</head>
<body>
<div class="result-card">
  <div class="result-icon <?=$ok ? 'ok' : 'bad';?>"><?=$ok ? '&#10004;' : '&#9888;';?></div>
  <h3><?=$ok ? 'Thank you!' : 'Payment status';?></h3>
  <div class="result-msg">
    <?php if (!empty($html_message)) echo $message; else echo '<p>' . html_escape($message) . '</p>'; ?>
  </div>
  <?php if (!empty($invoice)): ?>
    <?php if (is_superadmin_loggedin()): ?>
      <a class="btn-back" href="<?=base_url('saas/transactions');?>">Back to transactions</a>
    <?php else: ?>
      <a class="btn-back" href="<?=base_url('billing/pay/' . (int)$invoice->id);?>">Back to invoice</a>
    <?php endif; ?>
  <?php else: ?>
    <a class="btn-back" href="<?=base_url();?>">Home</a>
  <?php endif; ?>
</div>
</body>
</html>

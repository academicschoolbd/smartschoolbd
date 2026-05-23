<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title">Billing settings
    <small>renewal cron + email template (no need to touch files)</small>
  </h2>
</header>

<?=form_open(base_url('saas/save_billing_settings'), ['class' => 'form-horizontal']);?>
<div class="panel-body">

<h4 style="margin-top:0">Auto-renewal cron</h4>

<div class="form-group">
  <label class="col-md-3 control-label">Grace days</label>
  <div class="col-md-9">
    <input type="number" name="renewal_grace_days" min="0" max="365" class="form-control" style="max-width:120px"
      value="<?=html_escape($settings['renewal_grace_days'] ?? '7');?>">
    <p class="help-block">How many days before <code>current_period_end</code> to auto-invoice. Default 7.</p>
  </div>
</div>

<div class="form-group">
  <label class="col-md-3 control-label">From name</label>
  <div class="col-md-9">
    <input type="text" name="renewal_email_from_name" class="form-control"
      value="<?=html_escape($settings['renewal_email_from_name'] ?? 'SmartSchool.bd Billing');?>">
  </div>
</div>

<div class="form-group">
  <label class="col-md-3 control-label">From email</label>
  <div class="col-md-9">
    <input type="email" name="renewal_email_from_email" class="form-control"
      value="<?=html_escape($settings['renewal_email_from_email'] ?? 'billing@smartschool.bd');?>">
  </div>
</div>

<div class="form-group">
  <label class="col-md-3 control-label">Email subject</label>
  <div class="col-md-9">
    <input type="text" name="renewal_email_subject" class="form-control"
      value="<?=html_escape($settings['renewal_email_subject'] ?? '');?>">
  </div>
</div>

<div class="form-group">
  <label class="col-md-3 control-label">Email body (HTML)</label>
  <div class="col-md-9">
    <textarea name="renewal_email_body" rows="12" class="form-control" style="font-family:monospace;font-size:12px;"><?=html_escape($settings['renewal_email_body'] ?? '');?></textarea>
    <p class="help-block">
      Placeholders (replaced at send time):
      <code>{invoice_no}</code>
      <code>{amount}</code>
      <code>{pay_url}</code>
      <code>{school_name}</code>
      <code>{due_date}</code>
      <code>{billing_contact_email}</code>
    </p>
  </div>
</div>

<hr>
<h4>Tenant-facing</h4>

<div class="form-group">
  <label class="col-md-3 control-label">Billing contact email</label>
  <div class="col-md-9">
    <input type="email" name="billing_contact_email" class="form-control"
      value="<?=html_escape($settings['billing_contact_email'] ?? 'billing@smartschool.bd');?>">
    <p class="help-block">Shown on the tenant manual pay page if no bank/MFS account is configured.</p>
  </div>
</div>

</div>
<footer class="panel-footer text-right">
  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
</footer>
<?=form_close();?>

<?=form_open(base_url('saas/run_renewal_cron_now'), [
  'style'    => 'padding:0 18px 18px 18px;',
  'onsubmit' => "return confirm('Run the renewal cron now? This creates invoices for subscriptions inside the grace window and sends pay-link emails.');",
]);?>
  <button type="submit" class="btn btn-warning">
    <i class="fas fa-bolt"></i> Run renewal cron now
  </button>
  <span class="text-muted small">
    Same behaviour as the daily cPanel cron. Use to backfill or test without SSH.
  </span>
<?=form_close();?>
</section>

<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title">
    Edit gateway: <?=html_escape($gateway->name);?>
    <small><code><?=html_escape($gateway->code);?></code></small>
  </h2>
</header>
<?=form_open(base_url('saas/save_payment_gateway/' . (int)$gateway->id), ['class' => 'form-horizontal', 'autocomplete' => 'off']);?>
<div class="panel-body">

<div class="form-group">
  <label class="col-md-3 control-label">Enabled</label>
  <div class="col-md-9">
    <label class="radio-inline"><input type="radio" name="is_enabled" value="1" <?=$gateway->is_enabled ? 'checked' : '';?>> Yes</label>
    <label class="radio-inline"><input type="radio" name="is_enabled" value="0" <?=!$gateway->is_enabled ? 'checked' : '';?>> No</label>
    <p class="help-block">Disabled gateways do not appear on the tenant pay page.</p>
  </div>
</div>

<div class="form-group">
  <label class="col-md-3 control-label">Mode</label>
  <div class="col-md-9">
    <label class="radio-inline"><input type="radio" name="is_sandbox" value="1" <?=$gateway->is_sandbox ? 'checked' : '';?>> Sandbox</label>
    <label class="radio-inline"><input type="radio" name="is_sandbox" value="0" <?=!$gateway->is_sandbox ? 'checked' : '';?>> Live</label>
    <p class="help-block">Sandbox uses the provider's test endpoints. Switch to Live only after a successful sandbox transaction.</p>
  </div>
</div>

<hr>
<h4 style="margin: 18px 0 6px 0;">Credentials</h4>
<p class="text-muted" style="margin-top:0;">Never paste credentials anywhere outside this form. They're stored encrypted at rest in <code>saas_payment_gateway.credentials_json</code>.</p>

<?php if (!empty($fields)): foreach ($fields as $f):
  $name        = $f['name'];
  $label       = $f['label'];
  $type        = $f['type']        ?? 'text';
  $required    = !empty($f['required']);
  $placeholder = $f['placeholder'] ?? '';
  $help        = $f['help']        ?? '';
  $val         = isset($values[$name]) ? $values[$name] : '';
?>
<div class="form-group">
  <label class="col-md-3 control-label" for="cred_<?=html_escape($name);?>">
    <?=html_escape($label);?>
    <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
  </label>
  <div class="col-md-9">
    <?php if ($type === 'textarea'): ?>
      <textarea id="cred_<?=html_escape($name);?>" name="cred[<?=html_escape($name);?>]" rows="6" class="form-control"
        placeholder="<?=html_escape($placeholder);?>"><?=html_escape($val);?></textarea>
    <?php else: ?>
      <input id="cred_<?=html_escape($name);?>" name="cred[<?=html_escape($name);?>]"
        type="<?=html_escape(in_array($type, ['password','email','url']) ? $type : 'text');?>"
        class="form-control"
        value="<?=html_escape($val);?>"
        placeholder="<?=html_escape($placeholder);?>"
        autocomplete="off" spellcheck="false">
    <?php endif; ?>
    <?php if ($help): ?><p class="help-block"><?=html_escape($help);?></p><?php endif; ?>
  </div>
</div>
<?php endforeach; else: ?>
<div class="alert alert-info">No structured fields are defined for this provider. Use the Advanced JSON panel below.</div>
<?php endif; ?>

<hr>
<details style="margin: 12px 0;">
  <summary style="cursor:pointer; color:#666;">
    <i class="fas fa-cog"></i> Advanced — paste raw JSON
  </summary>
  <div style="margin-top:10px;">
    <p class="text-muted">If your provider needs keys not shown above, paste a JSON object here. <strong>Leave this blank to use the structured fields.</strong> If you fill any structured field above, this textarea is ignored on save.</p>
    <textarea name="credentials_json" rows="10" class="form-control" style="font-family:monospace; font-size:12px;" placeholder="<?=html_escape($sample_json);?>"></textarea>
    <pre style="background:#f6f8fa;padding:10px;border-radius:6px;font-size:12px; margin-top:6px;">Expected keys for <code><?=html_escape($gateway->code);?></code>: <?=html_escape($expected_keys);?></pre>
  </div>
</details>

</div>
<footer class="panel-footer text-right">
  <a href="<?=base_url('saas/payment_gateways');?>" class="btn btn-default">Cancel</a>
  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
</footer>
<?=form_close();?>
</section>

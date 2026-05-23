<?php
$p = isset($pkg) ? $pkg : null;
// Null-safe getter: handles either missing $p or missing column on $p.
// stdClass + ?? falls back cleanly without PHP 8.2 deprecation warnings.
$g = static function ($obj, $col, $default = '') {
    if (!$obj) return $default;
    return isset($obj->$col) && $obj->$col !== null ? $obj->$col : $default;
};
?>
<section class="panel">
<header class="panel-heading"><h2 class="panel-title"><?=$p ? translate('edit_plan').' · '.html_escape($g($p,'name')) : translate('new_plan');?></h2></header>
<form method="post" class="form-horizontal" action="<?=current_url();?>">
<div class="panel-body">
  <div class="form-group"><label class="col-md-3 control-label">Code</label>
    <div class="col-md-6"><input class="form-control" name="code" value="<?=html_escape($g($p,'code'));?>" required></div></div>
  <div class="form-group"><label class="col-md-3 control-label">Name</label>
    <div class="col-md-6"><input class="form-control" name="name" value="<?=html_escape($g($p,'name'));?>" required></div></div>
  <div class="form-group"><label class="col-md-3 control-label">Price (BDT / month)</label>
    <div class="col-md-3"><input class="form-control" type="number" step="0.01" name="price_bdt" value="<?=html_escape((string)$g($p,'price_bdt',0));?>"></div></div>
  <div class="form-group"><label class="col-md-3 control-label">Price (USD)</label>
    <div class="col-md-3"><input class="form-control" type="number" step="0.01" name="price_usd" value="<?=html_escape((string)$g($p,'price_usd'));?>"></div></div>
  <div class="form-group"><label class="col-md-3 control-label">Billing period</label>
    <div class="col-md-3"><select class="form-control" name="billing_period">
      <?php foreach (['monthly','yearly','lifetime'] as $bp): ?>
        <option value="<?=$bp;?>" <?=$g($p,'billing_period')===$bp?'selected':'';?>><?=$bp;?></option>
      <?php endforeach; ?>
    </select></div></div>
  <hr><h4>Limits (blank = unlimited)</h4>
  <?php foreach (['student_limit'=>'Students','staff_limit'=>'Staff','teacher_limit'=>'Teachers','parents_limit'=>'Parents'] as $k=>$lbl): ?>
    <div class="form-group"><label class="col-md-3 control-label"><?=$lbl;?></label>
      <div class="col-md-3"><input class="form-control" type="number" name="<?=$k;?>" value="<?=html_escape((string)$g($p,$k));?>"></div></div>
  <?php endforeach; ?>
  <hr><h4>Features &amp; extra limits (JSON)</h4>
  <div class="form-group"><label class="col-md-3 control-label">Features (comma-separated)</label>
    <div class="col-md-9"><input class="form-control" name="features" value="<?=$p?html_escape(implode(',', json_decode((string)$g($p,'features','[]'), true) ?: [])):'';?>" placeholder="dashboard,student,staff,…"></div></div>
  <div class="form-group"><label class="col-md-3 control-label">Limits JSON</label>
    <div class="col-md-9"><textarea class="form-control" rows="3" name="limits_json"><?=html_escape((string)$g($p,'limits','{}'));?></textarea>
      <small>e.g. {"storage_mb":2000,"custom_domain":true,"api":false,"sms":true}</small></div></div>
  <hr>
  <div class="form-group"><label class="col-md-3 control-label">Default trial?</label>
    <div class="col-md-6"><label class="checkbox-inline"><input type="checkbox" name="is_default_trial" value="1" <?=$g($p,'is_default_trial')?'checked':'';?>> Yes</label>
      Days: <input type="number" name="trial_days" value="<?=html_escape((string)$g($p,'trial_days',14));?>" style="width:80px" class="form-control input-inline"></div></div>
  <div class="form-group"><label class="col-md-3 control-label">Sort order</label>
    <div class="col-md-2"><input class="form-control" type="number" name="sort_order" value="<?=html_escape((string)$g($p,'sort_order',0));?>"></div></div>
  <div class="form-group"><label class="col-md-3 control-label">Active?</label>
    <div class="col-md-3"><label class="checkbox-inline"><input type="checkbox" name="is_active" value="1" <?=!$p||$g($p,'is_active',1)?'checked':'';?>> Yes</label></div></div>
  <div class="form-group"><label class="col-md-3 control-label">Description</label>
    <div class="col-md-9"><textarea class="form-control" rows="2" name="description"><?=html_escape((string)$g($p,'description'));?></textarea></div></div>
</div>
<div class="panel-footer">
  <button name="submit" value="save" class="btn btn-primary"><?=translate('save')?></button>
  <a href="<?=base_url('saas/package');?>" class="btn btn-default"><?=translate('cancel')?></a>
</div>
</form>
</section>

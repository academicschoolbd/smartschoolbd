<?php $r = isset($row) ? $row : null; ?>
<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=$r ? translate('edit') . ' · ' . html_escape($r->url) : translate('add');?></h2>
</header>
<?=form_open(uri_string(), ['class' => 'form-horizontal'])?>
<div class="panel-body">
  <div class="form-group">
    <label class="col-md-3 control-label"><?=translate('branch')?> *</label>
    <div class="col-md-6">
      <select class="form-control" name="school_id" required>
        <option value=""><?=translate('select')?></option>
        <?php foreach ($branches as $b): ?>
          <option value="<?=$b->id;?>" <?=$r && (int)$r->school_id === (int)$b->id ? 'selected' : '';?>>
            <?=html_escape($b->name);?><?=$b->subdomain ? ' · ' . html_escape($b->subdomain) : '';?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label"><?=translate('url')?> *</label>
    <div class="col-md-6">
      <input class="form-control" name="url" value="<?=$r ? html_escape($r->url) : '';?>" placeholder="e.g. ngps.smartschool.bd or app.example.com" required>
      <small class="text-muted">Lowercase. No scheme (no <code>https://</code>). No trailing slash.</small>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label"><?=translate('type')?></label>
    <div class="col-md-3">
      <select class="form-control" name="domain_type">
        <?php foreach (['subdomain', 'custom'] as $t): ?>
          <option value="<?=$t;?>" <?=$r && $r->domain_type === $t ? 'selected' : '';?>><?=$t;?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label"><?=translate('status')?></label>
    <div class="col-md-3">
      <label class="checkbox-inline">
        <input type="checkbox" name="status" value="1" <?=(!$r || (int)$r->status === 1) ? 'checked' : '';?>>
        <?=translate('active')?>
      </label>
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-3 control-label"><?=translate('notes')?></label>
    <div class="col-md-9">
      <textarea class="form-control" rows="2" name="notes"><?=$r ? html_escape($r->notes ?? '') : '';?></textarea>
    </div>
  </div>
</div>
<div class="panel-footer">
  <button name="submit" value="save" class="btn btn-primary"><?=translate('save')?></button>
  <a href="<?=base_url('custom_domain/list');?>" class="btn btn-default"><?=translate('cancel')?></a>
</div>
<?=form_close()?>
</section>

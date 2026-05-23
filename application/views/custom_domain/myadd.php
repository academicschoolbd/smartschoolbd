<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=translate('request_custom_domain')?></h2>
</header>
<?=form_open(uri_string(), ['class' => 'form-horizontal'])?>
<div class="panel-body">
  <p class="text-muted">
    Point your domain (e.g. <code>app.yourschool.com</code>) to SmartSchool.bd by adding a CNAME record:
  </p>
  <pre>app.yourschool.com  CNAME  smartschool.bd.</pre>
  <p class="text-muted">After adding the CNAME at your DNS provider, submit the form below. SmartSchool.bd will probe DNS and activate the domain once it resolves.</p>
  <div class="form-group">
    <label class="col-md-3 control-label"><?=translate('url')?> *</label>
    <div class="col-md-6">
      <input class="form-control" name="url" placeholder="app.yourschool.com" required>
    </div>
  </div>
</div>
<div class="panel-footer">
  <button name="submit" value="save" class="btn btn-primary"><?=translate('submit_request')?></button>
  <a href="<?=base_url('custom_domain/mylist');?>" class="btn btn-default"><?=translate('cancel')?></a>
</div>
<?=form_close()?>
</section>

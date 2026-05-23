<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=translate('dns_instruction')?> · <?=html_escape($row->url)?></h2>
</header>
<div class="panel-body">
  <h4>Step 1 — Add a DNS record</h4>
  <?php if ($row->domain_type === 'subdomain'): ?>
    <p>This is a SmartSchool.bd subdomain — already covered by the wildcard <code>*.smartschool.bd</code> A record. No DNS action required on your side.</p>
  <?php else: ?>
    <p>At your DNS provider, add a CNAME record:</p>
    <pre><?=html_escape($row->url)?>.   CNAME   smartschool.bd.</pre>
    <p>If your DNS provider doesn't allow CNAME at the apex of your domain (a.k.a. CNAME flattening), use the A record listed in your DNS provider for <code>smartschool.bd</code> directly.</p>
  <?php endif; ?>

  <h4>Step 2 — Wait for propagation</h4>
  <p>DNS propagation can take a few minutes to a few hours. SmartSchool.bd will probe automatically on each <em>Verify</em> click.</p>

  <h4>Step 3 — Status</h4>
  <p>
    <?php if ((int)$row->status === 1): ?>
      <span class="badge badge-success"><?=translate('active')?></span>
      Your custom domain is live. Visit
      <a href="https://<?=html_escape($row->url)?>" target="_blank">https://<?=html_escape($row->url)?></a>.
    <?php else: ?>
      <span class="badge badge-warning"><?=translate('pending')?></span>
      Not yet verified. After your DNS record is in place, ask SmartSchool.bd super-admin to click <em>Verify</em> on this row.
    <?php endif; ?>
  </p>

  <p>
    <a href="<?=base_url(is_superadmin_loggedin() ? 'custom_domain/list' : 'custom_domain/mylist')?>" class="btn btn-default">
      <?=translate('back')?>
    </a>
  </p>
</div>
</section>

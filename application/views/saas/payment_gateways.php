<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title">Payment gateways</h2>
</header>
<div class="panel-body">
<p class="text-muted">Enable a provider and click <em>Edit</em> to paste its credentials. Each provider's credentials live in
<code>saas_payment_gateway.credentials_json</code>; nothing here is committed to the repo.</p>
<table class="table table-bordered table-hover table-condensed mb-none">
<thead><tr>
  <th>#</th>
  <th>Code</th>
  <th>Name</th>
  <th>Enabled</th>
  <th>Mode</th>
  <th>Recurring</th>
  <th>Credentials</th>
  <th class="no-sort">Action</th>
</tr></thead>
<tbody>
<?php $i = 1; foreach ($gateways as $g): ?>
<tr>
  <td><?=$i++;?></td>
  <td><code><?=html_escape($g->code);?></code></td>
  <td><?=html_escape($g->name);?></td>
  <td>
    <?php if ($g->is_enabled): ?>
      <span class="badge badge-success">Enabled</span>
    <?php else: ?>
      <span class="badge badge-default">Disabled</span>
    <?php endif; ?>
  </td>
  <td><?=$g->is_sandbox ? '<span class="badge badge-warning">Sandbox</span>' : '<span class="badge badge-info">Live</span>';?></td>
  <td><?=$g->supports_recurring ? 'Yes' : 'No';?></td>
  <td><?=empty($g->credentials_json) ? '<span class="text-muted">(empty)</span>' : '<span class="text-success">Set</span>';?></td>
  <td>
    <a href="<?=base_url('saas/payment_gateway_edit/' . (int)$g->id);?>" class="btn btn-default btn-circle icon" title="Edit"><i class="fas fa-pen-nib"></i></a>
    <?=form_open(base_url('saas/toggle_payment_gateway/' . (int)$g->id), ['style' => 'display:inline']);?>
      <button type="submit" class="btn <?=$g->is_enabled ? 'btn-warning' : 'btn-success';?> btn-circle icon" title="<?=$g->is_enabled ? 'Disable' : 'Enable';?>">
        <i class="fas <?=$g->is_enabled ? 'fa-pause' : 'fa-play';?>"></i>
      </button>
    <?=form_close();?>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></section>

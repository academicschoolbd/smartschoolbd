<section class="panel">
<header class="panel-heading"><h2 class="panel-title"><?=translate('approved_schools')?></h2></header>
<div class="panel-body">
<?php if (empty($requests)): ?>
  <p><?=translate('no_records_found')?></p>
<?php else: ?>
<table class="table table-bordered table-hover table-condensed mb-none">
<thead><tr>
  <th>#</th><th><?=translate('school_name')?></th><th><?=translate('subdomain')?></th>
  <th><?=translate('owner')?></th><th><?=translate('plan')?></th>
  <th><?=translate('processed_at')?></th><th><?=translate('branch')?> ID</th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($requests as $r): ?>
<tr>
  <td><?=$i++;?></td>
  <td><?=html_escape($r->school_name);?></td>
  <td><a href="https://<?=html_escape($r->subdomain);?>.smartschool.bd" target="_blank"><?=html_escape($r->subdomain);?>.smartschool.bd</a></td>
  <td><?=html_escape($r->owner_name);?> (<?=html_escape($r->owner_email);?>)</td>
  <td><?=html_escape($r->package_name);?></td>
  <td><?=$r->processed_at;?></td>
  <td><?=$r->branch_id;?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php endif; ?>
</div></section>

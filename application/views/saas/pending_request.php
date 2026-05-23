<section class="panel">
<header class="panel-heading"><h2 class="panel-title"><?=translate('pending_request')?></h2></header>
<div class="panel-body">
<?php if (empty($requests)): ?>
  <p><?=translate('no_records_found')?></p>
<?php else: ?>
<table class="table table-bordered table-hover table-condensed mb-none">
<thead><tr>
  <th>#</th><th><?=translate('school_name')?></th><th><?=translate('subdomain')?></th>
  <th><?=translate('owner')?></th><th><?=translate('email')?></th><th><?=translate('phone')?></th>
  <th><?=translate('plan')?></th><th><?=translate('applied_at')?></th><th class="no-sort"><?=translate('action')?></th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($requests as $r): ?>
<tr>
  <td><?=$i++;?></td>
  <td><?=html_escape($r->school_name);?><?php if($r->school_name_bn): ?><br><small><?=html_escape($r->school_name_bn);?></small><?php endif; ?></td>
  <td><?=html_escape($r->subdomain);?>.smartschool.bd</td>
  <td><?=html_escape($r->owner_name);?></td>
  <td><a href="mailto:<?=html_escape($r->owner_email);?>"><?=html_escape($r->owner_email);?></a></td>
  <td><?=html_escape($r->owner_phone);?></td>
  <td><?=html_escape($r->package_name);?></td>
  <td><?=$r->created_at;?></td>
  <td>
    <a href="<?=base_url('saas/approve/'.$r->id);?>" class="btn btn-success btn-sm" onclick="return confirm('Approve and create branch?')"><?=translate('approve')?></a>
    <a href="<?=base_url('saas/reject/'.$r->id);?>"  class="btn btn-danger btn-sm"  onclick="return confirm('Reject?')"><?=translate('reject')?></a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php endif; ?>
</div></section>

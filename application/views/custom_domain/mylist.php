<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=translate('custom_domain')?></h2>
  <div class="panel-actions">
    <a class="btn btn-primary btn-xs" href="<?=base_url('custom_domain/myadd')?>">
      <i class="fas fa-plus"></i> <?=translate('add')?>
    </a>
  </div>
</header>
<div class="panel-body">
<table class="table table-bordered table-hover table-condensed mb-none">
<thead><tr>
  <th width="50">#</th>
  <th><?=translate('url')?></th>
  <th><?=translate('type')?></th>
  <th><?=translate('status')?></th>
  <th class="no-sort" width="180"><?=translate('action')?></th>
</tr></thead>
<tbody>
<?php $i = 1; foreach ($rows as $r): ?>
<tr>
  <td><?=$i++;?></td>
  <td><?=html_escape($r->url);?></td>
  <td><span class="badge badge-info"><?=html_escape($r->domain_type);?></span></td>
  <td>
    <?php $st = (int)$r->status; ?>
    <span class="badge badge-<?=$st === 1 ? 'success' : 'warning';?>">
      <?=$st === 1 ? translate('active') : translate('pending');?>
    </span>
  </td>
  <td>
    <a class="btn btn-info btn-xs" href="<?=base_url('custom_domain/dns_instruction/' . $r->id)?>"><?=translate('dns_instruction')?></a>
  </td>
</tr>
<?php endforeach; ?>
<?php if (empty($rows)): ?>
<tr><td colspan="5" class="text-center text-muted"><?=translate('no_data_available')?></td></tr>
<?php endif; ?>
</tbody></table>
</div>
</section>

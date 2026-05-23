<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=translate('custom_domain')?></h2>
  <div class="panel-actions">
    <a class="btn btn-primary btn-xs" href="<?=base_url('custom_domain/add')?>">
      <i class="fas fa-plus"></i> <?=translate('add')?>
    </a>
  </div>
</header>
<div class="panel-body">
<table class="table table-bordered table-hover table-condensed mb-none table-export">
<thead><tr>
  <th width="50">#</th>
  <th><?=translate('branch')?></th>
  <th><?=translate('url')?></th>
  <th><?=translate('type')?></th>
  <th><?=translate('status')?></th>
  <th><?=translate('notes')?></th>
  <th class="no-sort" width="220"><?=translate('action')?></th>
</tr></thead>
<tbody>
<?php $i = 1; foreach ($rows as $r): ?>
<tr>
  <td><?=$i++;?></td>
  <td><?=html_escape($r->branch_name ?? '#' . $r->school_id);?></td>
  <td>
    <a href="https://<?=html_escape($r->url);?>" target="_blank"><?=html_escape($r->url);?></a>
  </td>
  <td><span class="badge badge-info"><?=html_escape($r->domain_type);?></span></td>
  <td>
    <?php $st = (int)$r->status; ?>
    <span class="badge badge-<?=$st === 1 ? 'success' : 'warning';?>">
      <?=$st === 1 ? translate('active') : translate('pending');?>
    </span>
  </td>
  <td><small class="text-muted"><?=html_escape($r->notes ?? '');?></small></td>
  <td>
    <a class="btn btn-default btn-xs" href="<?=base_url('custom_domain/edit/' . $r->id)?>">
      <i class="fas fa-pen"></i>
    </a>
    <?=form_open('custom_domain/verify/' . $r->id, ['style' => 'display:inline'])?>
      <button class="btn btn-info btn-xs" title="<?=translate('verify')?>"><i class="fas fa-check"></i></button>
    <?=form_close()?>
    <?=form_open('custom_domain/toggle/' . $r->id, ['style' => 'display:inline'])?>
      <button class="btn btn-warning btn-xs" title="<?=translate('toggle_status')?>"><i class="fas fa-power-off"></i></button>
    <?=form_close()?>
    <?=form_open('custom_domain/delete/' . $r->id, ['style' => 'display:inline', 'onsubmit' => "return confirm('" . translate('are_you_sure') . "')"])?>
      <button class="btn btn-danger btn-xs" title="<?=translate('delete')?>"><i class="fas fa-trash"></i></button>
    <?=form_close()?>
  </td>
</tr>
<?php endforeach; ?>
<?php if (empty($rows)): ?>
<tr><td colspan="7" class="text-center text-muted"><?=translate('no_data_available')?></td></tr>
<?php endif; ?>
</tbody></table>
</div>
</section>

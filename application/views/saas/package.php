<section class="panel">
<header class="panel-heading">
  <div class="panel-actions"><a href="<?=base_url('saas/package_edit');?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> <?=translate('new_plan')?></a></div>
  <h2 class="panel-title"><?=translate('plan')?></h2>
</header>
<div class="panel-body">
<table class="table table-bordered table-hover table-condensed mb-none">
<thead><tr>
  <th>#</th><th><?=translate('code')?></th><th><?=translate('name')?></th>
  <th><?=translate('price')?></th><th><?=translate('billing_period')?></th>
  <th><?=translate('student_limit')?></th><th><?=translate('staff_limit')?></th>
  <th><?=translate('trial')?></th><th><?=translate('active')?></th>
  <th class="no-sort"><?=translate('action')?></th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($packages as $p): ?>
<tr>
  <td><?=$i++;?></td>
  <td><code><?=html_escape($p->code);?></code></td>
  <td><?=html_escape($p->name);?></td>
  <td>৳<?=number_format((float)$p->price_bdt,0);?>
    <?php if ($p->price_usd !== null): ?> / $<?=number_format((float)$p->price_usd,0);?><?php endif; ?>
  </td>
  <td><?=$p->billing_period;?></td>
  <td><?=$p->student_limit === null ? '∞' : (int)$p->student_limit;?></td>
  <td><?=$p->staff_limit   === null ? '∞' : (int)$p->staff_limit;?></td>
  <td><?php if ($p->is_default_trial): ?><span class="badge badge-warning"><?=(int)$p->trial_days?>d</span><?php endif; ?></td>
  <td><?=$p->is_active ? '<span class="badge badge-success">Y</span>' : '<span class="badge badge-default">N</span>';?></td>
  <td>
    <a href="<?=base_url('saas/package_edit/'.$p->id);?>" class="btn btn-default btn-circle icon"><i class="fas fa-pen-nib"></i></a>
    <a href="<?=base_url('saas/package_delete/'.$p->id);?>" onclick="return confirm('Delete plan?')" class="btn btn-danger btn-circle icon"><i class="fas fa-trash"></i></a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></section>

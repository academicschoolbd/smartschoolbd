<?php $s = $subscription; ?>
<section class="panel">
<header class="panel-heading"><h2 class="panel-title"><?=translate('your_subscription')?></h2></header>
<div class="panel-body">
<?php if (!$s): ?>
  <div class="alert alert-warning">No subscription assigned. Contact support.</div>
<?php else: ?>
  <div class="row">
    <div class="col-md-6">
      <h3><?=html_escape($s->package_name);?>
        <small><span class="badge badge-<?=$s->status==='active'?'success':($s->status==='trial'?'warning':'danger');?>"><?=$s->status;?></span></small>
      </h3>
      <p>৳<?=number_format((float)$s->price_bdt, 0);?> / month</p>
      <p><strong>Period:</strong> <?=$s->current_period_start;?> → <?=$s->current_period_end;?></p>
      <?php if ($s->status === 'trial' && $s->trial_ends_at): ?>
        <p><strong>Trial ends:</strong> <?=$s->trial_ends_at;?>
          (<?=max(0, (int)((strtotime($s->trial_ends_at) - time()) / 86400));?> days left)</p>
      <?php endif; ?>
    </div>
    <div class="col-md-6">
      <h4><?=translate('usage')?></h4>
      <p>Students: <?=$usage['students'];?>
        <?php if ($s->student_limit !== null): ?>
          / <?=$s->student_limit;?>
          <?php $pct = $s->student_limit > 0 ? min(100, ($usage['students']/$s->student_limit)*100) : 0; ?>
          <div class="progress"><div class="progress-bar <?=$pct>=100?'progress-bar-danger':($pct>=80?'progress-bar-warning':'progress-bar-success');?>" style="width:<?=$pct;?>%"></div></div>
        <?php endif; ?>
      </p>
      <p>Staff: <?=$usage['staff'];?>
        <?php if ($s->staff_limit !== null): ?>
          / <?=$s->staff_limit;?>
          <?php $pct2 = $s->staff_limit > 0 ? min(100, ($usage['staff']/$s->staff_limit)*100) : 0; ?>
          <div class="progress"><div class="progress-bar <?=$pct2>=100?'progress-bar-danger':($pct2>=80?'progress-bar-warning':'progress-bar-success');?>" style="width:<?=$pct2;?>%"></div></div>
        <?php endif; ?>
      </p>
    </div>
  </div>
<?php endif; ?>
</div></section>

<section class="panel">
<header class="panel-heading"><h2 class="panel-title"><?=translate('change_plan')?></h2></header>
<div class="panel-body">
<form method="post" action="<?=base_url('subscription/upgrade');?>" class="form-inline">
  <select name="package_id" class="form-control">
    <?php foreach ($packages as $p): ?>
      <option value="<?=$p->id;?>" <?=$s && $s->package_id==$p->id?'selected':'';?>>
        <?=html_escape($p->name);?> · ৳<?=number_format((float)$p->price_bdt,0);?>/mo
      </option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-primary"><?=translate('upgrade')?></button>
</form>
</div></section>

<section class="panel">
<header class="panel-heading"><h2 class="panel-title"><?=translate('invoices')?></h2></header>
<div class="panel-body">
<table class="table table-bordered table-hover table-condensed mb-none">
<thead><tr><th>#</th><th>Invoice no</th><th>Period</th><th>Amount</th><th>Status</th><th>Due</th><th class="no-sort"><?=translate('action')?></th></tr></thead>
<tbody>
<?php $i=1; foreach ($invoices as $inv): ?>
<tr>
  <td><?=$i++;?></td><td><?=html_escape($inv->invoice_no);?></td>
  <td><?=$inv->period_start;?> → <?=$inv->period_end;?></td>
  <td>৳<?=number_format((float)$inv->amount,2);?></td>
  <td><span class="badge badge-<?=$inv->status==='paid'?'success':'warning';?>"><?=$inv->status;?></span></td>
  <td><?=$inv->due_date;?></td>
  <td>
    <?php if ($inv->status !== 'paid'): ?>
      <a href="<?=base_url('billing/pay/'.(int)$inv->id);?>" class="btn btn-success btn-sm">
        <i class="fas fa-credit-card"></i> <?=translate('pay_now')?: 'Pay now';?>
      </a>
    <?php else: ?>
      <span class="text-muted">—</span>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
<?php if (empty($invoices)): ?><tr><td colspan="7"><em><?=translate('no_records_found')?></em></td></tr><?php endif; ?>
</tbody></table>
</div></section>

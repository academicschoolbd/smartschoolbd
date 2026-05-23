<section class="panel">
<header class="panel-heading"><h2 class="panel-title">Invoices</h2></header>
<div class="panel-body">
<?=form_open('saas/create_invoice', ['class'=>'form-inline mb-md'])?>
  <label>Branch ID</label> <input type="number" name="branch_id" class="form-control input-sm" style="width:80px" required>
  <label>Amount (BDT)</label> <input type="number" name="amount" class="form-control input-sm" style="width:120px" required>
  <button class="btn btn-default btn-sm">Create invoice</button>
<?=form_close()?>
<table class="table table-bordered table-hover table-condensed mb-none">
<thead><tr>
  <th>#</th><th>Invoice no</th><th>Branch</th><th>Period</th>
  <th>Amount</th><th>Status</th><th>Due</th><th>Paid at</th><th class="no-sort">Action</th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($invoices as $inv): ?>
<tr>
  <td><?=$i++;?></td>
  <td><?=html_escape($inv->invoice_no);?></td>
  <td><?=html_escape($inv->branch_name);?><?php if($inv->subdomain): ?> (<?=html_escape($inv->subdomain);?>)<?php endif; ?></td>
  <td><?=$inv->period_start;?> → <?=$inv->period_end;?></td>
  <td><?=$inv->currency;?> <?=number_format((float)$inv->amount, 2);?></td>
  <td><span class="badge badge-<?=$inv->status==='paid'?'success':($inv->status==='open'?'warning':'default');?>"><?=$inv->status;?></span></td>
  <td><?=$inv->due_date;?></td>
  <td><?=$inv->paid_at?:'-';?></td>
  <td>
    <?php if ($inv->status !== 'paid'): ?>
      <?=form_open('saas/mark_paid/'.$inv->id, ['style'=>'display:inline;margin:0;', 'onsubmit'=>"return confirm('Mark this invoice paid (manual)?')"])?>
        <button class="btn btn-success btn-sm">Mark paid</button>
      <?=form_close()?>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></section>

<section class="panel">
<header class="panel-heading"><h2 class="panel-title">Payments</h2></header>
<div class="panel-body">
<table class="table table-bordered table-hover table-condensed mb-none">
<thead><tr>
  <th>#</th><th>Invoice</th><th>Branch</th><th>Amount</th>
  <th>Provider</th><th>Txn ID</th><th>Status</th><th>Paid at</th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($payments as $pay): ?>
<tr>
  <td><?=$i++;?></td>
  <td><?=html_escape($pay->invoice_no);?></td>
  <td><?=html_escape($pay->branch_name);?></td>
  <td><?=$pay->currency;?> <?=number_format((float)$pay->amount, 2);?></td>
  <td><?=$pay->provider;?></td>
  <td><code><?=html_escape($pay->provider_txn_id);?></code></td>
  <td><span class="badge badge-<?=$pay->status==='succeeded'?'success':'default';?>"><?=$pay->status;?></span></td>
  <td><?=$pay->paid_at;?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></section>

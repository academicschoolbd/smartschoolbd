<?php
// Pre-compute the unique provider, status and branch lists for the filter
// dropdowns so the view doesn't have to special-case empty arrays.
$providers = [];
$statuses  = [];
$branches  = [];
foreach ($payments as $p) {
    if ($p->provider && !in_array($p->provider, $providers, true)) $providers[] = $p->provider;
    if ($p->status   && !in_array($p->status, $statuses, true))    $statuses[]  = $p->status;
    if ($p->branch_name && !isset($branches[$p->branch_name])) $branches[$p->branch_name] = $p->branch_name;
}
foreach ($invoices as $inv) {
    if ($inv->branch_name && !isset($branches[$inv->branch_name])) $branches[$inv->branch_name] = $inv->branch_name;
    if ($inv->status      && !in_array($inv->status, $statuses, true)) $statuses[] = $inv->status;
}
ksort($branches);
?>
<section class="panel">
<header class="panel-heading"><h2 class="panel-title">Invoices</h2></header>
<div class="panel-body">

<?=form_open('saas/create_invoice', ['class'=>'form-inline mb-md'])?>
  <label>Branch ID</label> <input type="number" name="branch_id" class="form-control input-sm" style="width:80px" required>
  <label>Amount (BDT)</label> <input type="number" name="amount" class="form-control input-sm" style="width:120px" required>
  <button class="btn btn-default btn-sm">Create invoice</button>
<?=form_close()?>

<div class="row mb-md" style="gap:6px; align-items:flex-end;">
  <div class="col-sm-2">
    <label class="control-label small mb-xs"><?=translate('from_date')?></label>
    <input type="date" id="inv-from" class="form-control input-sm">
  </div>
  <div class="col-sm-2">
    <label class="control-label small mb-xs"><?=translate('to_date')?></label>
    <input type="date" id="inv-to" class="form-control input-sm">
  </div>
  <div class="col-sm-2">
    <label class="control-label small mb-xs"><?=translate('status')?></label>
    <select id="inv-status" class="form-control input-sm">
      <option value=""><?=translate('all')?></option>
      <option value="open">open</option>
      <option value="paid">paid</option>
      <option value="void">void</option>
    </select>
  </div>
  <div class="col-sm-3">
    <label class="control-label small mb-xs"><?=translate('branch')?></label>
    <select id="inv-branch" class="form-control input-sm">
      <option value=""><?=translate('all')?></option>
      <?php foreach ($branches as $b): ?>
        <option value="<?=html_escape($b)?>"><?=html_escape($b)?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-sm-2">
    <label class="control-label small mb-xs">&nbsp;</label>
    <a href="<?=base_url('saas/export_transactions')?>" class="btn btn-default btn-sm btn-block">
      <i class="fa fa-file-download"></i> <?=translate('export_csv')?>
    </a>
  </div>
</div>

<table id="tbl-invoices" class="table table-bordered table-hover table-condensed mb-none table-export">
<thead><tr>
  <th>#</th><th>Invoice no</th><th>Branch</th><th>Period</th>
  <th>Amount</th><th>Status</th><th>Due</th><th>Paid at</th><th class="no-sort">Action</th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($invoices as $inv): ?>
<tr>
  <td><?=$i++;?></td>
  <td><?=html_escape($inv->invoice_no);?></td>
  <td>
    <a href="<?=base_url('saas/tenant/'.(int)$inv->branch_id)?>"><?=html_escape($inv->branch_name);?></a>
    <?php if($inv->subdomain): ?> <small>(<?=html_escape($inv->subdomain);?>)</small><?php endif; ?>
  </td>
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

<div class="row mb-md" style="gap:6px; align-items:flex-end;">
  <div class="col-sm-2">
    <label class="control-label small mb-xs"><?=translate('from_date')?></label>
    <input type="date" id="pay-from" class="form-control input-sm">
  </div>
  <div class="col-sm-2">
    <label class="control-label small mb-xs"><?=translate('to_date')?></label>
    <input type="date" id="pay-to" class="form-control input-sm">
  </div>
  <div class="col-sm-2">
    <label class="control-label small mb-xs"><?=translate('provider')?></label>
    <select id="pay-provider" class="form-control input-sm">
      <option value=""><?=translate('all')?></option>
      <?php foreach ($providers as $pv): ?>
        <option value="<?=html_escape($pv)?>"><?=html_escape($pv)?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-sm-2">
    <label class="control-label small mb-xs"><?=translate('status')?></label>
    <select id="pay-status" class="form-control input-sm">
      <option value=""><?=translate('all')?></option>
      <option value="succeeded">succeeded</option>
      <option value="pending">pending</option>
      <option value="failed">failed</option>
    </select>
  </div>
  <div class="col-sm-3">
    <label class="control-label small mb-xs"><?=translate('branch')?></label>
    <select id="pay-branch" class="form-control input-sm">
      <option value=""><?=translate('all')?></option>
      <?php foreach ($branches as $b): ?>
        <option value="<?=html_escape($b)?>"><?=html_escape($b)?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>

<table id="tbl-payments" class="table table-bordered table-hover table-condensed mb-none table-export">
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

<script>
(function () {
  // Custom DataTables filter for date-range columns. Each row registers
  // (tableId, dateColIndex, fromInputId, toInputId) and the function below
  // is invoked for every row of every filtered DataTable.
  var dateFilters = [];
  $.fn.dataTable.ext.search.push(function (settings, rowData) {
    var tableId = settings.nTable.id;
    for (var i = 0; i < dateFilters.length; i++) {
      var f = dateFilters[i];
      if (f.table !== tableId) continue;
      var raw = (rowData[f.col] || '').trim();
      if (!raw || raw === '-') continue;
      // Inv period column "YYYY-MM-DD → YYYY-MM-DD" — fall back to the start date.
      var match = raw.match(/\d{4}-\d{2}-\d{2}/);
      if (!match) continue;
      var rowDate = match[0];
      var from = $('#' + f.from).val();
      var to   = $('#' + f.to).val();
      if (from && rowDate < from) return false;
      if (to   && rowDate > to)   return false;
    }
    return true;
  });

  function init() {
    if (!window.jQuery || !$.fn.DataTable) return setTimeout(init, 50);
    if (!$.fn.DataTable.isDataTable('#tbl-invoices') || !$.fn.DataTable.isDataTable('#tbl-payments')) {
      return setTimeout(init, 50);
    }
    var invT = $('#tbl-invoices').DataTable();
    var payT = $('#tbl-payments').DataTable();

    // ---- Invoices filters (cols: 0#, 1invoice_no, 2branch, 3period, 4amount, 5status, 6due, 7paid_at)
    dateFilters.push({table: 'tbl-invoices', col: 3, from: 'inv-from', to: 'inv-to'});
    $('#inv-from, #inv-to').on('change', function () { invT.draw(); });
    $('#inv-status').on('change', function () {
      invT.column(5).search(this.value ? ('\\b' + this.value + '\\b') : '', true, false).draw();
    });
    $('#inv-branch').on('change', function () {
      invT.column(2).search(this.value ? this.value : '', false, true).draw();
    });

    // ---- Payments filters (cols: 0#, 1invoice, 2branch, 3amount, 4provider, 5txn, 6status, 7paid_at)
    dateFilters.push({table: 'tbl-payments', col: 7, from: 'pay-from', to: 'pay-to'});
    $('#pay-from, #pay-to').on('change', function () { payT.draw(); });
    $('#pay-provider').on('change', function () {
      payT.column(4).search(this.value ? ('\\b' + this.value + '\\b') : '', true, false).draw();
    });
    $('#pay-status').on('change', function () {
      payT.column(6).search(this.value ? ('\\b' + this.value + '\\b') : '', true, false).draw();
    });
    $('#pay-branch').on('change', function () {
      payT.column(2).search(this.value ? this.value : '', false, true).draw();
    });
  }
  if (document.readyState === 'complete') init(); else $(window).on('load', init);
})();
</script>

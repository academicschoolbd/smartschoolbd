<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=translate('school_subscription')?></h2>
</header>
<div class="panel-body">

<!-- Filter row + bulk-action toolbar -->
<div class="row mb-md saas-school-toolbar" style="gap:6px; align-items:flex-end;">
  <div class="col-sm-2">
    <label class="control-label small mb-xs"><?=translate('status')?></label>
    <select id="filter-status" class="form-control input-sm">
      <option value=""><?=translate('all')?></option>
      <option value="trial">trial</option>
      <option value="active">active</option>
      <option value="past_due">past_due</option>
      <option value="suspended">suspended</option>
      <option value="cancelled">cancelled</option>
    </select>
  </div>
  <div class="col-sm-3">
    <label class="control-label small mb-xs"><?=translate('plan')?></label>
    <select id="filter-plan" class="form-control input-sm">
      <option value=""><?=translate('all')?></option>
      <?php foreach ($packages as $p): ?>
        <option value="<?=html_escape($p->name)?>"><?=html_escape($p->name)?> · ৳<?=number_format((float)$p->price_bdt, 0)?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-sm-2">
    <label class="control-label small mb-xs">&nbsp;</label>
    <a href="<?=base_url('saas/export_subscriptions')?>" class="btn btn-default btn-sm btn-block">
      <i class="fa fa-file-download"></i> <?=translate('export_csv')?>
    </a>
  </div>
  <div class="col-sm-5 text-right" id="bulk-actions" style="display:none;">
    <label class="control-label small mb-xs">
      <span id="bulk-count">0</span> <?=translate('selected')?>
    </label>
    <div class="btn-group btn-group-sm" role="group">
      <?=form_open('saas/bulk_suspend', ['id'=>'frm-bulk-suspend',  'class'=>'bulk-form', 'style'=>'display:inline;margin:0;', 'onsubmit'=>"return confirm('Suspend selected tenants?')"])?>
        <button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-pause"></i> <?=translate('bulk_suspend')?></button>
      <?=form_close()?>
      <?=form_open('saas/bulk_activate', ['id'=>'frm-bulk-activate', 'class'=>'bulk-form', 'style'=>'display:inline;margin:0;'])?>
        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-play"></i> <?=translate('bulk_activate')?></button>
      <?=form_close()?>
      <?=form_open('saas/bulk_extend', ['id'=>'frm-bulk-extend', 'class'=>'bulk-form form-inline', 'style'=>'display:inline-flex;gap:4px;align-items:center;margin:0;'])?>
        <input type="number" name="days" min="1" value="30" class="form-control input-sm" style="width:60px">
        <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-plus"></i> <?=translate('bulk_extend')?></button>
      <?=form_close()?>
    </div>
  </div>
</div>

<table id="tbl-school" class="table table-bordered table-hover table-condensed mb-none table-export">
<thead><tr>
  <th class="no-sort no-export" width="34"><input type="checkbox" id="chk-all"></th>
  <th width="50">#</th>
  <th><?=translate('branch')?></th>
  <th><?=translate('subdomain')?></th>
  <th><?=translate('plan')?></th>
  <th><?=translate('status')?></th>
  <th><?=translate('trial_ends_at')?></th>
  <th><?=translate('expires')?></th>
  <th class="no-sort" width="280"><?=translate('action')?></th>
</tr></thead>
<tbody>
<?php $i=1; foreach($subscriptions as $row): ?>
<tr>
  <td><input type="checkbox" class="chk-row" value="<?=(int)$row->school_id?>"></td>
  <td><?=$i++;?></td>
  <td>
    <a href="<?=base_url('saas/tenant/'.(int)$row->school_id)?>"><?=html_escape($row->branch_name);?></a>
  </td>
  <td>
    <?php if($row->subdomain): ?>
      <a href="https://<?=html_escape($row->subdomain);?>.smartschool.bd" target="_blank"><?=html_escape($row->subdomain);?>.smartschool.bd</a>
    <?php else: ?>—<?php endif; ?>
  </td>
  <td><span class="badge badge-info"><?=html_escape($row->package_name);?></span> · ৳<?=number_format((float)$row->price_bdt, 0);?></td>
  <td>
    <?php
      $cls = ['trial'=>'warning','active'=>'success','past_due'=>'warning','suspended'=>'danger','cancelled'=>'default'];
      $c   = $cls[$row->status] ?? 'default';
    ?>
    <span class="badge badge-<?=$c;?>"><?=html_escape($row->status);?></span>
  </td>
  <td><?=$row->trial_ends_at;?></td>
  <td><?=$row->expire_date;?></td>
  <td class="min-w-c">
    <a href="<?=base_url('saas/tenant/'.(int)$row->school_id)?>" class="btn btn-default btn-sm" title="<?=translate('tenant_detail')?>"><i class="fa fa-eye"></i></a>
    <?=form_open('saas/extend/'.$row->school_id, ['style'=>'display:inline-flex;gap:4px;align-items:center;margin:0;'])?>
      <input type="number" name="days" min="1" value="30" style="width:60px" class="form-control input-sm">
      <button class="btn btn-default btn-sm"><?=translate('extend')?></button>
    <?=form_close()?>
    <?php if($row->status !== 'suspended'): ?>
      <?=form_open('saas/suspend/'.$row->school_id, ['style'=>'display:inline;margin:0;', 'onsubmit'=>"return confirm('Suspend tenant?')"])?>
        <button class="btn btn-warning btn-sm"><?=translate('suspend')?></button>
      <?=form_close()?>
    <?php else: ?>
      <?=form_open('saas/activate/'.$row->school_id, ['style'=>'display:inline;margin:0;'])?>
        <button class="btn btn-success btn-sm"><?=translate('activate')?></button>
      <?=form_close()?>
    <?php endif; ?>
    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#assign-<?=$row->school_id?>"><?=translate('change_plan')?></button>
  </td>
</tr>
<div class="modal fade" id="assign-<?=$row->school_id?>"><div class="modal-dialog"><div class="modal-content">
  <?=form_open('saas/assign_package')?>
    <input type="hidden" name="branch_id" value="<?=$row->school_id?>">
    <div class="modal-header"><h4 class="modal-title">Change plan · <?=html_escape($row->branch_name)?></h4></div>
    <div class="modal-body">
      <label>Plan</label>
      <select name="package_id" class="form-control">
        <?php foreach($packages as $p): ?>
          <option value="<?=$p->id?>" <?=$p->id==$row->package_id?'selected':''?>><?=html_escape($p->name)?> · ৳<?=number_format((float)$p->price_bdt,0)?></option>
        <?php endforeach; ?>
      </select>
      <label class="mt-md">Status</label>
      <select name="status" class="form-control">
        <option value="active">active</option>
        <option value="trial">trial</option>
        <option value="past_due">past_due</option>
      </select>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Save</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    </div>
  <?=form_close()?>
</div></div></div>
<?php endforeach; ?>
</tbody></table>
</div>
</section>

<script>
(function () {
  // Wait for DataTables to wire up the table-export class (assets/js/app.js runs $('.table-export').DataTable()
  // synchronously on DOM ready, so deferring until window.load is enough).
  function init() {
    if (!window.jQuery || !$.fn.DataTable || !$.fn.DataTable.isDataTable('#tbl-school')) {
      return setTimeout(init, 50);
    }
    var table = $('#tbl-school').DataTable();

    // Column indexes match the <thead>: 0=checkbox, 1=#, 2=branch, 3=subdomain, 4=plan, 5=status, 6=trial, 7=expires, 8=action
    $('#filter-plan').on('change', function () {
      var v = this.value;
      // Plan cell contains the package name inside a badge; use a contains match.
      table.column(4).search(v ? v : '', false, true).draw();
    });
    $('#filter-status').on('change', function () {
      var v = this.value;
      // Status cell contains the status word inside a badge; exact word match.
      table.column(5).search(v ? ('\\b' + v + '\\b') : '', true, false).draw();
    });

    // Bulk-select wiring.
    var $tbl = $('#tbl-school');
    var $bulk = $('#bulk-actions');
    var $count = $('#bulk-count');
    function refreshBulkBar() {
      var n = $tbl.find('tbody .chk-row:checked').length;
      $count.text(n);
      $bulk.toggle(n > 0);
    }
    // Re-bind on DataTables redraw because rows on other pages get detached.
    $tbl.on('change', '.chk-row', refreshBulkBar);
    $('#chk-all').on('change', function () {
      var checked = this.checked;
      // Only toggle rows visible on the current page (matches user intent for bulk ops).
      $tbl.find('tbody tr:visible .chk-row').prop('checked', checked);
      refreshBulkBar();
    });
    table.on('draw', refreshBulkBar);

    // On submit of any bulk form, attach the selected branch_ids[].
    $('.bulk-form').on('submit', function () {
      var $form = $(this);
      $form.find('input[name="branch_ids[]"]').remove();
      $tbl.find('tbody .chk-row:checked').each(function () {
        $('<input>').attr({type: 'hidden', name: 'branch_ids[]', value: this.value}).appendTo($form);
      });
      if ($tbl.find('tbody .chk-row:checked').length === 0) {
        alert('No tenants selected.');
        return false;
      }
    });
  }
  if (document.readyState === 'complete') init(); else $(window).on('load', init);
})();
</script>

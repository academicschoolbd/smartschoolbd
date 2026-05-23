<section class="panel">
<header class="panel-heading"><h2 class="panel-title"><?=translate('pending_request')?></h2></header>
<div class="panel-body">
<?php if (empty($requests)): ?>
  <p><?=translate('no_records_found')?></p>
<?php else: ?>

<div class="row mb-md" id="bulk-actions" style="display:none; gap:6px; align-items:flex-end;">
  <div class="col-sm-12 text-right">
    <label class="control-label small mb-xs">
      <span id="bulk-count">0</span> <?=translate('selected')?>
    </label>
    <div class="btn-group btn-group-sm" role="group">
      <?=form_open('saas/bulk_approve', ['id'=>'frm-bulk-approve', 'class'=>'bulk-form', 'style'=>'display:inline;margin:0;', 'onsubmit'=>"return confirm('Approve and provision selected tenants?')"])?>
        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> <?=translate('bulk_approve')?></button>
      <?=form_close()?>
      <?=form_open('saas/bulk_reject', ['id'=>'frm-bulk-reject', 'class'=>'bulk-form', 'style'=>'display:inline;margin:0;', 'onsubmit'=>"return confirm('Reject selected requests?')"])?>
        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> <?=translate('bulk_reject')?></button>
      <?=form_close()?>
    </div>
  </div>
</div>

<table id="tbl-pending" class="table table-bordered table-hover table-condensed mb-none table-export">
<thead><tr>
  <th class="no-sort no-export" width="34"><input type="checkbox" id="chk-all"></th>
  <th>#</th><th><?=translate('school_name')?></th><th><?=translate('subdomain')?></th>
  <th><?=translate('owner')?></th><th><?=translate('email')?></th><th><?=translate('phone')?></th>
  <th><?=translate('plan')?></th><th><?=translate('applied_at')?></th><th class="no-sort"><?=translate('action')?></th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($requests as $r): ?>
<tr>
  <td><input type="checkbox" class="chk-row" value="<?=(int)$r->id?>"></td>
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

<script>
(function () {
  function init() {
    if (!window.jQuery || !$.fn.DataTable || !$.fn.DataTable.isDataTable('#tbl-pending')) {
      return setTimeout(init, 50);
    }
    var $tbl = $('#tbl-pending');
    var $bulk = $('#bulk-actions');
    var $count = $('#bulk-count');
    function refreshBulkBar() {
      var n = $tbl.find('tbody .chk-row:checked').length;
      $count.text(n);
      $bulk.toggle(n > 0);
    }
    $tbl.on('change', '.chk-row', refreshBulkBar);
    $('#chk-all').on('change', function () {
      $tbl.find('tbody tr:visible .chk-row').prop('checked', this.checked);
      refreshBulkBar();
    });
    $tbl.DataTable().on('draw', refreshBulkBar);

    $('.bulk-form').on('submit', function () {
      var $form = $(this);
      $form.find('input[name="request_ids[]"]').remove();
      $tbl.find('tbody .chk-row:checked').each(function () {
        $('<input>').attr({type:'hidden', name:'request_ids[]', value:this.value}).appendTo($form);
      });
      if ($tbl.find('tbody .chk-row:checked').length === 0) {
        alert('No requests selected.');
        return false;
      }
    });
  }
  if (document.readyState === 'complete') init(); else $(window).on('load', init);
})();
</script>

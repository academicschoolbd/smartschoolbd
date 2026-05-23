<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=translate('school_subscription')?></h2>
</header>
<div class="panel-body">
<table class="table table-bordered table-hover table-condensed mb-none table-export">
<thead><tr>
  <th width="50">#</th>
  <th><?=translate('branch')?></th>
  <th><?=translate('subdomain')?></th>
  <th><?=translate('plan')?></th>
  <th><?=translate('status')?></th>
  <th><?=translate('trial_ends_at')?></th>
  <th><?=translate('expires')?></th>
  <th class="no-sort" width="240"><?=translate('action')?></th>
</tr></thead>
<tbody>
<?php $i=1; foreach($subscriptions as $row): ?>
<tr>
  <td><?=$i++;?></td>
  <td><?=html_escape($row->branch_name);?></td>
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

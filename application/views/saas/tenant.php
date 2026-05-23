<?php
$sub = $subscription ?? null;
$statusCls = [
    'trial'    => 'warning',
    'active'   => 'success',
    'past_due' => 'warning',
    'suspended'=> 'danger',
    'cancelled'=> 'default',
];
$cls = $sub ? ($statusCls[$sub->status] ?? 'default') : 'default';
?>
<div class="row">
  <div class="col-md-12">
    <a href="<?=base_url('saas/school')?>" class="btn btn-default btn-sm mb-md">
      <i class="fa fa-arrow-left"></i> <?=translate('back_to_list') ?: 'Back to subscriptions'?>
    </a>
  </div>
</div>

<div class="row">
  <!-- Branch summary -->
  <div class="col-md-6">
    <section class="panel">
      <header class="panel-heading"><h2 class="panel-title"><?=translate('branch') ?: 'Branch'?> #<?=(int)$branch->id?></h2></header>
      <div class="panel-body">
        <dl class="dl-horizontal">
          <dt><?=translate('name') ?: 'Name'?></dt>
          <dd><?=html_escape($branch->name)?></dd>
          <?php if (!empty($branch->school_name) && $branch->school_name !== $branch->name): ?>
            <dt><?=translate('school_name')?></dt>
            <dd><?=html_escape($branch->school_name)?></dd>
          <?php endif; ?>
          <dt><?=translate('subdomain')?></dt>
          <dd>
            <?php if (!empty($branch->subdomain)): ?>
              <a href="https://<?=html_escape($branch->subdomain)?>.smartschool.bd" target="_blank">
                <?=html_escape($branch->subdomain)?>.smartschool.bd
              </a>
            <?php else: ?>—<?php endif; ?>
          </dd>
          <dt><?=translate('email')?></dt>
          <dd><a href="mailto:<?=html_escape($branch->email)?>"><?=html_escape($branch->email)?></a></dd>
          <dt><?=translate('phone') ?: 'Phone'?></dt>
          <dd><?=html_escape($branch->mobileno)?></dd>
          <dt><?=translate('timezone') ?: 'Timezone'?></dt>
          <dd><?=html_escape($branch->timezone ?? '')?></dd>
          <dt><?=translate('status')?></dt>
          <dd><?=((int)$branch->status === 1) ? '<span class="badge badge-success">active</span>' : '<span class="badge badge-default">inactive</span>'?></dd>
        </dl>
        <?php if (!empty($custom_domains)): ?>
          <hr>
          <h5><?=translate('custom_domains') ?: 'Custom domains'?></h5>
          <ul class="list-unstyled mb-none">
            <?php foreach ($custom_domains as $cd): ?>
              <li>
                <code><?=html_escape($cd->url)?></code>
                · <small><?=html_escape($cd->domain_type)?></small>
                · <?=((int)$cd->status === 1) ? '<span class="badge badge-success">enabled</span>' : '<span class="badge badge-default">disabled</span>'?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </section>
  </div>

  <!-- Subscription summary -->
  <div class="col-md-6">
    <section class="panel">
      <header class="panel-heading"><h2 class="panel-title"><?=translate('subscription') ?: 'Subscription'?></h2></header>
      <div class="panel-body">
        <?php if ($sub): ?>
        <dl class="dl-horizontal">
          <dt><?=translate('plan')?></dt>
          <dd>
            <span class="badge badge-info"><?=html_escape($sub->package_name)?></span>
            · ৳<?=number_format((float)($sub->price_bdt ?? 0), 0)?>
          </dd>
          <dt><?=translate('status')?></dt>
          <dd><span class="badge badge-<?=$cls?>"><?=html_escape($sub->status)?></span></dd>
          <dt><?=translate('trial_ends_at')?></dt>
          <dd><?=$sub->trial_ends_at ?: '—'?></dd>
          <dt><?=translate('current_period') ?: 'Period'?></dt>
          <dd><?=$sub->current_period_start?> → <?=$sub->current_period_end?></dd>
          <dt><?=translate('expires')?></dt>
          <dd><?=$sub->expire_date?></dd>
        </dl>

        <hr>
        <div class="btn-group btn-group-sm" role="group">
          <?=form_open('saas/extend/'.(int)$branch->id, ['style'=>'display:inline-flex;gap:4px;align-items:center;margin:0;'])?>
            <input type="number" name="days" min="1" value="30" class="form-control input-sm" style="width:60px">
            <button class="btn btn-default btn-sm"><?=translate('extend')?></button>
          <?=form_close()?>
          <?php if ($sub->status !== 'suspended'): ?>
            <?=form_open('saas/suspend/'.(int)$branch->id, ['style'=>'display:inline;margin:0;', 'onsubmit'=>"return confirm('Suspend tenant?')"])?>
              <button class="btn btn-warning btn-sm"><?=translate('suspend')?></button>
            <?=form_close()?>
          <?php else: ?>
            <?=form_open('saas/activate/'.(int)$branch->id, ['style'=>'display:inline;margin:0;'])?>
              <button class="btn btn-success btn-sm"><?=translate('activate')?></button>
            <?=form_close()?>
          <?php endif; ?>
          <?=form_open('saas/cancel/'.(int)$branch->id, ['style'=>'display:inline;margin:0;', 'onsubmit'=>"return confirm('Cancel subscription?')"])?>
            <button class="btn btn-danger btn-sm"><?=translate('cancel')?></button>
          <?=form_close()?>
          <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#assign-plan">
            <?=translate('change_plan')?>
          </button>
        </div>

        <div class="modal fade" id="assign-plan"><div class="modal-dialog"><div class="modal-content">
          <?=form_open('saas/assign_package')?>
            <input type="hidden" name="branch_id" value="<?=(int)$branch->id?>">
            <div class="modal-header"><h4 class="modal-title">Change plan · <?=html_escape($branch->name)?></h4></div>
            <div class="modal-body">
              <label>Plan</label>
              <select name="package_id" class="form-control">
                <?php foreach ($packages as $p): ?>
                  <option value="<?=$p->id?>" <?=$p->id == $sub->package_id ? 'selected' : ''?>>
                    <?=html_escape($p->name)?> · ৳<?=number_format((float)$p->price_bdt, 0)?>
                  </option>
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
        <?php else: ?>
          <p class="text-muted"><?=translate('no_records_found')?></p>
          <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#assign-plan">
            <?=translate('assign_plan') ?: 'Assign plan'?>
          </button>
          <div class="modal fade" id="assign-plan"><div class="modal-dialog"><div class="modal-content">
            <?=form_open('saas/assign_package')?>
              <input type="hidden" name="branch_id" value="<?=(int)$branch->id?>">
              <div class="modal-header"><h4 class="modal-title">Assign plan · <?=html_escape($branch->name)?></h4></div>
              <div class="modal-body">
                <label>Plan</label>
                <select name="package_id" class="form-control">
                  <?php foreach ($packages as $p): ?>
                    <option value="<?=$p->id?>"><?=html_escape($p->name)?> · ৳<?=number_format((float)$p->price_bdt, 0)?></option>
                  <?php endforeach; ?>
                </select>
                <label class="mt-md">Status</label>
                <select name="status" class="form-control">
                  <option value="active">active</option>
                  <option value="trial">trial</option>
                </select>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            <?=form_close()?>
          </div></div></div>
        <?php endif; ?>
      </div>
    </section>
  </div>
</div>

<!-- Invoices -->
<section class="panel">
<header class="panel-heading"><h2 class="panel-title">Invoices</h2></header>
<div class="panel-body">
  <?php if (empty($invoices)): ?>
    <p class="text-muted"><?=translate('no_records_found')?></p>
  <?php else: ?>
  <table class="table table-bordered table-hover table-condensed mb-none">
    <thead><tr>
      <th>#</th><th>Invoice no</th><th>Period</th><th>Amount</th>
      <th>Status</th><th>Due</th><th>Paid at</th><th class="no-sort">Action</th>
    </tr></thead>
    <tbody>
    <?php $i=1; foreach ($invoices as $inv): ?>
      <tr>
        <td><?=$i++?></td>
        <td><?=html_escape($inv->invoice_no)?></td>
        <td><?=$inv->period_start?> → <?=$inv->period_end?></td>
        <td><?=$inv->currency?> <?=number_format((float)$inv->amount, 2)?></td>
        <td><span class="badge badge-<?=$inv->status==='paid'?'success':($inv->status==='open'?'warning':'default')?>"><?=$inv->status?></span></td>
        <td><?=$inv->due_date?></td>
        <td><?=$inv->paid_at ?: '-'?></td>
        <td>
          <?php if ($inv->status !== 'paid'): ?>
            <?=form_open('saas/mark_paid/'.(int)$inv->id, ['style'=>'display:inline;margin:0;', 'onsubmit'=>"return confirm('Mark this invoice paid (manual)?')"])?>
              <button class="btn btn-success btn-sm">Mark paid</button>
            <?=form_close()?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
</section>

<!-- Payments -->
<section class="panel">
<header class="panel-heading"><h2 class="panel-title">Payments</h2></header>
<div class="panel-body">
  <?php if (empty($payments)): ?>
    <p class="text-muted"><?=translate('no_records_found')?></p>
  <?php else: ?>
  <table class="table table-bordered table-hover table-condensed mb-none">
    <thead><tr>
      <th>#</th><th>Invoice</th><th>Amount</th><th>Provider</th>
      <th>Txn ID</th><th>Status</th><th>Paid at</th>
    </tr></thead>
    <tbody>
    <?php $i=1; foreach ($payments as $pay): ?>
      <tr>
        <td><?=$i++?></td>
        <td>#<?=(int)$pay->invoice_id?></td>
        <td><?=$pay->currency?> <?=number_format((float)$pay->amount, 2)?></td>
        <td><?=$pay->provider?></td>
        <td><code><?=html_escape($pay->provider_txn_id)?></code></td>
        <td><span class="badge badge-<?=$pay->status==='succeeded'?'success':'default'?>"><?=$pay->status?></span></td>
        <td><?=$pay->paid_at?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
</section>

<!-- Audit log -->
<section class="panel">
<header class="panel-heading"><h2 class="panel-title"><?=translate('audit_log') ?: 'Audit log'?></h2></header>
<div class="panel-body">
  <?php if (empty($audit_log)): ?>
    <p class="text-muted">
      <?=translate('no_records_found')?>.
      <small>(<?=translate('audit_log_table_optional') ?: 'Audit log table is optional — apply migration 008_audit_log.sql to enable.'?>)</small>
    </p>
  <?php else: ?>
  <table class="table table-bordered table-hover table-condensed mb-none">
    <thead><tr>
      <th>#</th><th><?=translate('when') ?: 'When'?></th><th><?=translate('actor') ?: 'Actor'?></th>
      <th><?=translate('action')?></th><th><?=translate('target') ?: 'Target'?></th>
      <th><?=translate('meta') ?: 'Meta'?></th><th>IP</th>
    </tr></thead>
    <tbody>
    <?php $i=1; foreach ($audit_log as $a): ?>
      <tr>
        <td><?=$i++?></td>
        <td><?=$a->created_at ?? '—'?></td>
        <td>
          <?=html_escape($a->actor_username ?? '')?>
          <?php if (!empty($a->actor_id)): ?><small>(#<?=(int)$a->actor_id?>)</small><?php endif; ?>
        </td>
        <td><code><?=html_escape($a->action)?></code></td>
        <td><?=html_escape($a->target_type)?> #<?=(int)$a->target_id?></td>
        <td>
          <?php if (!empty($a->meta)): ?>
            <small><code><?=html_escape($a->meta)?></code></small>
          <?php endif; ?>
        </td>
        <td><small><?=html_escape($a->ip ?? '')?></small></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
</section>

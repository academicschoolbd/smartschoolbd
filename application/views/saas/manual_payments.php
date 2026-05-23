<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title">Manual payment submissions
    <small>tenant proof of bank/MFS transfer awaiting approval</small>
  </h2>
</header>
<div class="panel-body">

<h4 style="margin-top:0">Pending review <span class="badge badge-warning"><?=count($pending);?></span></h4>

<?php if (empty($pending)): ?>
  <p class="text-muted"><em>No submissions are currently waiting for review.</em></p>
<?php else: ?>
<table class="table table-bordered table-hover table-condensed">
<thead><tr>
  <th>#</th><th>Submitted</th><th>School</th><th>Invoice</th><th>Amount</th>
  <th>Ref</th><th>Payer</th><th>Receipt</th><th>Action</th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($pending as $s): ?>
<tr>
  <td><?=$i++;?></td>
  <td class="text-nowrap"><?=html_escape($s->created_at);?></td>
  <td><?=html_escape($s->branch_name);?></td>
  <td>
    <a href="<?=base_url('billing/pay/'.(int)$s->invoice_id);?>" target="_blank"><?=html_escape($s->invoice_no);?></a>
  </td>
  <td>৳<?=number_format((float)$s->invoice_amount, 2);?></td>
  <td><code><?=html_escape($s->txn_ref);?></code></td>
  <td>
    <?php if ($s->payer_name): ?><?=html_escape($s->payer_name);?><br><?php endif; ?>
    <?php if ($s->payer_phone): ?><small class="text-muted"><?=html_escape($s->payer_phone);?></small><?php endif; ?>
  </td>
  <td>
    <?php if ($s->screenshot_path): ?>
      <a href="<?=base_url($s->screenshot_path);?>" target="_blank">
        <i class="fas fa-image"></i> view
      </a>
    <?php else: ?>
      <span class="text-muted">—</span>
    <?php endif; ?>
  </td>
  <td>
    <?=form_open(base_url('saas/approve_manual_payment/'.(int)$s->id), ['style' => 'display:inline']);?>
      <button type="submit" class="btn btn-success btn-sm"
        onclick="return confirm('Approve this submission and mark invoice <?=html_escape($s->invoice_no);?> as PAID?');">
        <i class="fas fa-check"></i> Approve
      </button>
    <?=form_close();?>
    <?=form_open(base_url('saas/reject_manual_payment/'.(int)$s->id), [
      'style'    => 'display:inline',
      'onsubmit' => "var n=prompt('Optional rejection reason for tenant:'); if(n===null) return false; this.review_notes.value=n; return true;",
    ]);?>
      <input type="hidden" name="review_notes">
      <button type="submit" class="btn btn-danger btn-sm">
        <i class="fas fa-times"></i> Reject
      </button>
    <?=form_close();?>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php endif; ?>

<hr>
<h4>History <small>(last 100)</small></h4>
<table class="table table-bordered table-condensed">
<thead><tr>
  <th>#</th><th>Submitted</th><th>Reviewed</th><th>School</th><th>Invoice</th>
  <th>Amount</th><th>Ref</th><th>Status</th><th>Notes</th>
</tr></thead>
<tbody>
<?php $i=1; foreach ($history as $s): ?>
<tr>
  <td><?=$i++;?></td>
  <td class="text-nowrap"><small><?=html_escape($s->created_at);?></small></td>
  <td class="text-nowrap"><small><?=html_escape($s->reviewed_at ?: '—');?></small></td>
  <td><?=html_escape($s->branch_name);?></td>
  <td><?=html_escape($s->invoice_no);?></td>
  <td>৳<?=number_format((float)$s->invoice_amount, 2);?></td>
  <td><code><?=html_escape($s->txn_ref);?></code></td>
  <td>
    <span class="badge badge-<?=$s->status==='approved'?'success':($s->status==='rejected'?'danger':'warning');?>">
      <?=html_escape(ucfirst($s->status));?>
    </span>
  </td>
  <td><small class="text-muted"><?=html_escape($s->review_notes ?: '');?></small></td>
</tr>
<?php endforeach; ?>
<?php if (empty($history)): ?>
<tr><td colspan="9"><em>No submissions yet.</em></td></tr>
<?php endif; ?>
</tbody></table>

</div>
</section>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manual payment · Invoice <?=html_escape($invoice->invoice_no);?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { background:#f5f7fa; }
    .billing-card { max-width: 760px; margin: 32px auto; box-shadow: 0 6px 24px rgba(0,0,0,0.08); border:0; }
    .billing-card .card-header { background:#1f9d55; color:#fff; border-bottom:0; padding:18px 24px; }
    .billing-card .card-header h3 { margin:0; font-weight:600; }
    .billing-card .card-body { padding:24px; }
    .bank-info { background:#f0f9f4; border-left:4px solid #1f9d55; padding:16px 20px; border-radius:6px; }
    .bank-info dt { color:#666; font-weight:500; margin-top:8px; }
    .bank-info dd { font-family: ui-monospace, "SF Mono", Menlo, Consolas, monospace; font-size:15px; margin-bottom:0; word-break: break-all; }
    .badge-amount { font-size:22px; padding:8px 14px; }
    .step-num { display:inline-flex; width:28px; height:28px; border-radius:50%; background:#1f9d55; color:#fff; align-items:center; justify-content:center; font-weight:bold; margin-right:8px; }
    .submitted-row { background:#fffbea; border-left:4px solid #f1c40f; padding:12px 14px; border-radius:6px; margin-top:14px; }
    .submitted-row.approved { background:#f0f9f4; border-left-color:#1f9d55; }
    .submitted-row.rejected { background:#fff3f3; border-left-color:#e74c3c; }
  </style>
</head>
<body>
<div class="container">
<?php if (function_exists('get_alert')) { $alert = get_alert(); if ($alert) echo $alert; } ?>

<div class="card billing-card">
  <div class="card-header">
    <h3><i class="fas fa-university"></i> Manual / Bank Transfer</h3>
  </div>
  <div class="card-body">

    <!-- Invoice summary -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <div class="text-muted small">Invoice</div>
        <div style="font-size:20px; font-weight:600;"><?=html_escape($invoice->invoice_no);?></div>
        <div class="text-muted small">School: <?=html_escape($branch->name ?? '');?></div>
      </div>
      <div class="text-right">
        <div class="text-muted small">Amount due</div>
        <span class="badge badge-success badge-amount">৳<?=number_format((float)$invoice->amount, 2);?></span>
      </div>
    </div>

    <!-- Step 1: instructions -->
    <h5 style="margin-top:18px;"><span class="step-num">1</span> Send the payment</h5>
    <?php
      $hasAcct = !empty($manual['account_number']);
      $hasInstructions = !empty($manual['instructions']);
    ?>
    <?php if ($hasAcct || $hasInstructions): ?>
    <div class="bank-info">
      <?php if (!empty($manual['bank_name'])): ?>
        <dt>Bank / MFS</dt><dd><?=html_escape($manual['bank_name']);?></dd>
      <?php endif; ?>
      <?php if (!empty($manual['account_name'])): ?>
        <dt>Account name</dt><dd><?=html_escape($manual['account_name']);?></dd>
      <?php endif; ?>
      <?php if (!empty($manual['account_number'])): ?>
        <dt>Account / wallet number</dt><dd><?=html_escape($manual['account_number']);?></dd>
      <?php endif; ?>
      <?php if (!empty($manual['routing_number'])): ?>
        <dt>Routing / IFSC</dt><dd><?=html_escape($manual['routing_number']);?></dd>
      <?php endif; ?>
      <?php if ($hasInstructions): ?>
        <dt>Instructions</dt><dd style="font-family:inherit; white-space:pre-wrap;"><?=html_escape($manual['instructions']);?></dd>
      <?php endif; ?>
      <dt>Reference (please use this)</dt><dd><strong><?=html_escape($invoice->invoice_no);?></strong></dd>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
      The platform admin hasn't configured manual payment details yet. Please contact
      <a href="mailto:<?=html_escape($billing_contact_email ?? 'billing@smartschool.bd');?>"><?=html_escape($billing_contact_email ?? 'billing@smartschool.bd');?></a>
      for wire instructions.
    </div>
    <?php endif; ?>

    <!-- Step 2: submit proof -->
    <h5 style="margin-top:24px;"><span class="step-num">2</span> Submit payment proof</h5>

    <?php if ($has_pending): ?>
      <div class="alert alert-info">
        <i class="fas fa-clock"></i> A submission is already pending review for this invoice. We'll email you the moment it's approved.
      </div>
    <?php else: ?>
      <?=form_open_multipart(base_url('billing/submit_manual/' . (int)$invoice->id));?>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Transaction reference <span class="text-danger">*</span></label>
            <input type="text" name="txn_ref" class="form-control" required maxlength="128"
              placeholder="bank txn id / MFS reference">
          </div>
          <div class="form-group col-md-6">
            <label>Paid on</label>
            <input type="date" name="paid_at" class="form-control" value="<?=date('Y-m-d');?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Payer name</label>
            <input type="text" name="payer_name" class="form-control" maxlength="255">
          </div>
          <div class="form-group col-md-6">
            <label>Payer phone</label>
            <input type="tel" name="payer_phone" class="form-control" maxlength="64">
          </div>
        </div>
        <div class="form-group">
          <label>Payer account / wallet number</label>
          <input type="text" name="payer_account" class="form-control" maxlength="128">
        </div>
        <div class="form-group">
          <label>Receipt / screenshot (image or PDF, max 4MB)</label>
          <input type="file" name="screenshot" class="form-control-file" accept="image/*,application/pdf">
        </div>
        <div class="form-group">
          <label>Notes (optional)</label>
          <textarea name="notes" rows="3" class="form-control" placeholder="Anything the reviewer should know"></textarea>
        </div>
        <button type="submit" class="btn btn-success btn-lg">
          <i class="fas fa-paper-plane"></i> Submit for review
        </button>
        <a href="<?=base_url('billing/pay/' . (int)$invoice->id);?>" class="btn btn-link">Choose another method</a>
      <?=form_close();?>
    <?php endif; ?>

    <!-- Step 3: existing submissions for this invoice -->
    <?php if (!empty($submissions)): ?>
      <hr>
      <h5><span class="step-num">3</span> Submission history</h5>
      <?php foreach ($submissions as $s): ?>
      <div class="submitted-row <?=html_escape($s->status);?>">
        <div class="d-flex justify-content-between">
          <div>
            <strong>Ref:</strong> <?=html_escape($s->txn_ref);?>
            &nbsp;·&nbsp;
            <strong>Submitted:</strong> <?=html_escape($s->created_at);?>
          </div>
          <div>
            <span class="badge badge-<?=$s->status==='approved'?'success':($s->status==='rejected'?'danger':'warning');?>">
              <?=html_escape(ucfirst($s->status));?>
            </span>
          </div>
        </div>
        <?php if (!empty($s->review_notes)): ?>
          <div class="text-muted small" style="margin-top:6px;">Reviewer notes: <?=html_escape($s->review_notes);?></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>

<p class="text-center text-muted small" style="margin-bottom:32px;">
  <a href="<?=base_url('subscription');?>"><i class="fas fa-arrow-left"></i> Back to subscription</a>
</p>
</div>
</body>
</html>

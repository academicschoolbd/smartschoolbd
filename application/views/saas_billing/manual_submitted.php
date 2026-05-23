<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submission received · Invoice <?=html_escape($invoice->invoice_no);?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { background:#f5f7fa; }
    .billing-card { max-width: 560px; margin: 80px auto; box-shadow: 0 6px 24px rgba(0,0,0,0.08); border:0; text-align:center; }
    .billing-card .icon { font-size:64px; color:#1f9d55; margin: 16px 0; }
  </style>
</head>
<body>
<div class="card billing-card">
  <div class="card-body" style="padding: 32px;">
    <div class="icon"><i class="fas fa-check-circle"></i></div>
    <h3>Submission received</h3>
    <p class="text-muted">We've recorded your payment proof for invoice <code><?=html_escape($invoice->invoice_no);?></code>.</p>
    <p>Reference: <strong><?=html_escape($submission->txn_ref ?? '');?></strong></p>
    <p class="text-muted small">An administrator will review and approve it shortly. You'll receive an email once the invoice is marked paid.</p>
    <hr>
    <a href="<?=base_url('subscription');?>" class="btn btn-primary">
      <i class="fas fa-arrow-left"></i> Back to subscription
    </a>
  </div>
</div>
</body>
</html>

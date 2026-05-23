<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=html_escape($title);?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
  body { background: #f7f9fc; font-family: system-ui, sans-serif; }
  .signup-card { max-width: 720px; margin: 40px auto; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
  .plan { border: 1px solid #dde2eb; border-radius: 8px; padding: 12px; cursor: pointer; }
  .plan input { margin-right: 8px; }
  .plan:hover { border-color: #2e7d32; }
  .plan.selected { border-color: #2e7d32; background: #e8f5e9; }
  .subdomain-input { display: flex; align-items: center; }
  .subdomain-input .form-control { flex: 1; }
  .subdomain-input .suffix { margin-left: 8px; color: #6b7280; font-family: monospace; }
  .brand { color: #2e7d32; }
</style>
</head>
<body>
<div class="container">
<div class="card signup-card">
  <div class="card-body p-4">
    <h2 class="brand">SmartSchool.bd</h2>
    <h4 class="mb-3">Sign up your school</h4>
    <p class="text-muted">14-day free trial. No card required. Your own subdomain in 5 minutes.</p>

    <?php if (validation_errors()): ?>
      <div class="alert alert-danger"><?=validation_errors();?></div>
    <?php endif; ?>

    <form method="post" action="<?=base_url('signup');?>">
      <div class="mb-3">
        <label class="form-label">School name (English) *</label>
        <input class="form-control" name="school_name" value="<?=set_value('school_name');?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">School name (Bangla)</label>
        <input class="form-control" name="school_name_bn" value="<?=set_value('school_name_bn');?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Subdomain *</label>
        <div class="subdomain-input">
          <input class="form-control" name="subdomain" pattern="[a-z0-9_-]{3,64}" value="<?=set_value('subdomain');?>" required>
          <span class="suffix">.smartschool.bd</span>
        </div>
        <small class="text-muted">Lowercase letters, numbers, hyphens. Min 3 chars.</small>
      </div>

      <hr>
      <h5>Choose your plan</h5>
      <div class="row g-2 mb-3">
        <?php foreach ($packages as $p): ?>
        <div class="col-md-4">
          <label class="plan d-block">
            <input type="radio" name="package_id" value="<?=$p->id;?>" <?=$p->is_default_trial?'checked':'';?>>
            <strong><?=html_escape($p->name);?></strong><br>
            <span class="text-muted">৳<?=number_format((float)$p->price_bdt,0);?> / mo</span><br>
            <small><?=html_escape($p->description);?></small>
          </label>
        </div>
        <?php endforeach; ?>
      </div>

      <hr>
      <h5>Owner contact</h5>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Your name *</label>
          <input class="form-control" name="owner_name" value="<?=set_value('owner_name');?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Phone *</label>
          <input class="form-control" name="owner_phone" value="<?=set_value('owner_phone');?>" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email *</label>
        <input class="form-control" type="email" name="owner_email" value="<?=set_value('owner_email');?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Notes (optional)</label>
        <textarea class="form-control" name="notes"><?=set_value('notes');?></textarea>
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="terms_accept" value="1" id="t" required>
        <label class="form-check-label" for="t">I agree to the Terms of Service and Privacy Policy.</label>
      </div>
      <button name="submit" value="apply" class="btn btn-success btn-lg">Sign up — start free trial</button>
    </form>
  </div>
</div>
<p class="text-center text-muted small">© <?=date('Y');?> SmartSchool.bd</p>
</div>
<script>
document.querySelectorAll('.plan input').forEach(r => {
  r.addEventListener('change', () => {
    document.querySelectorAll('.plan').forEach(p => p.classList.remove('selected'));
    r.closest('.plan').classList.add('selected');
  });
  if (r.checked) r.closest('.plan').classList.add('selected');
});
</script>
</body></html>

<?php
defined('BASEPATH') or exit('No direct script access allowed');
$s = isset($s) ? $s : (object)[];
$success = $this->session->flashdata('flash_success');
$error   = $this->session->flashdata('flash_error');
?>
<section class="panel">
  <header class="panel-heading">
    <h2 class="panel-title">
      <i class="fas fa-globe-asia"></i>
      Landing page · apex (<?=htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'smartschool.bd')?>)
    </h2>
    <div class="panel-actions">
      <a target="_blank" href="<?=base_url('landing?variant=a')?>" class="btn btn-default btn-xs">Preview A</a>
      <a target="_blank" href="<?=base_url('landing?variant=b')?>" class="btn btn-default btn-xs">Preview B</a>
      <a target="_blank" href="<?=base_url('landing?variant=c')?>" class="btn btn-default btn-xs">Preview C</a>
      <a target="_blank" href="<?=base_url('landing?variant=d')?>" class="btn btn-default btn-xs">Preview D</a>
      <a target="_blank" href="<?=base_url('landing?variant=e')?>" class="btn btn-default btn-xs">Preview E</a>
      <a target="_blank" href="<?=base_url('landing')?>" class="btn btn-primary btn-xs">Open live landing</a>
    </div>
  </header>

  <?php if ($success): ?>
    <div class="alert alert-success" style="margin:14px 22px 0">
      <i class="fas fa-check-circle"></i> <?=html_escape($success)?>
    </div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger" style="margin:14px 22px 0">
      <i class="fas fa-exclamation-triangle"></i> <?=html_escape($error)?>
    </div>
  <?php endif; ?>

  <form method="post" class="form-horizontal" action="<?=base_url('saas/landing/save')?>">
    <input type="hidden" name="<?=$this->security->get_csrf_token_name()?>" value="<?=$this->security->get_csrf_hash()?>">

    <div class="panel-body">

      <!-- VARIANT -->
      <h4 style="margin-top:0">Active variant</h4>
      <p class="text-muted small">Pick which template renders at <code>https://smartschool.bd/</code>. Both variants pull from the same copy fields below — switching only changes the visual layout.</p>
      <div class="form-group">
        <div class="col-md-12">
          <label class="radio-inline" style="margin-right:24px">
            <input type="radio" name="active_variant" value="a" <?=($s->active_variant ?? 'a') === 'a' ? 'checked' : ''?>>
            <strong>Variant A — Bold Gradient</strong>
            <span class="text-muted">(green gradient hero, free-tier callout card, animated reveals)</span>
          </label>
          <br>
          <label class="radio-inline" style="margin-top:8px">
            <input type="radio" name="active_variant" value="b" <?=($s->active_variant ?? 'a') === 'b' ? 'checked' : ''?>>
            <strong>Variant B — Clean Minimal</strong>
            <span class="text-muted">(white background, big typography, brand accent only, Stripe-ish)</span>
          </label>
          <br>
          <label class="radio-inline" style="margin-top:8px">
            <input type="radio" name="active_variant" value="c" <?=($s->active_variant ?? 'a') === 'c' ? 'checked' : ''?>>
            <strong>Variant C — Dark Mode / Night</strong>
            <span class="text-muted">(near-black background, white text, brand-colour accents, sleek &amp; modern)</span>
          </label>
          <br>
          <label class="radio-inline" style="margin-top:8px">
            <input type="radio" name="active_variant" value="d" <?=($s->active_variant ?? 'a') === 'd' ? 'checked' : ''?>>
            <strong>Variant D — Playful / Illustration</strong>
            <span class="text-muted">(rounded cards, pastel brand-tinted backgrounds, soft shadows, Notion/Canva feel)</span>
          </label>
          <br>
          <label class="radio-inline" style="margin-top:8px">
            <input type="radio" name="active_variant" value="e" <?=($s->active_variant ?? 'a') === 'e' ? 'checked' : ''?>>
            <strong>Variant E — Corporate / Formal</strong>
            <span class="text-muted">(serif headings, navy + brand accents, formal layout for established schools)</span>
          </label>
        </div>
      </div>

      <hr>

      <!-- BRAND COLOUR -->
      <h4>Brand colour</h4>
      <div class="form-group">
        <label class="col-md-3 control-label">Primary colour</label>
        <div class="col-md-3">
          <input class="form-control" type="color" name="brand_color"
                 value="<?=html_escape($s->brand_color ?? '#1f9d55')?>"
                 style="height:38px;padding:4px">
        </div>
        <div class="col-md-6">
          <p class="form-control-static small text-muted">Used for buttons, accents and the gradient hero on Variant A. Hex only.</p>
        </div>
      </div>

      <hr>

      <!-- HERO -->
      <h4>Hero copy</h4>
      <div class="form-group">
        <label class="col-md-3 control-label">Eyebrow line</label>
        <div class="col-md-9"><input class="form-control" name="hero_eyebrow" maxlength="160"
          value="<?=html_escape($s->hero_eyebrow ?? '')?>"
          placeholder="Free for every Bangladeshi school — no card, no limits"></div>
      </div>
      <div class="form-group">
        <label class="col-md-3 control-label">Headline (H1)</label>
        <div class="col-md-9"><input class="form-control" name="hero_h1" required maxlength="255"
          value="<?=html_escape($s->hero_h1 ?? '')?>"
          placeholder="Run your school in 5 minutes — on us."></div>
      </div>
      <div class="form-group">
        <label class="col-md-3 control-label">Bengali subtitle</label>
        <div class="col-md-9"><input class="form-control" name="hero_bn" maxlength="255"
          value="<?=html_escape($s->hero_bn ?? '')?>"
          placeholder="বাংলায় সাবটাইটেল…"></div>
      </div>
      <div class="form-group">
        <label class="col-md-3 control-label">Lead paragraph</label>
        <div class="col-md-9"><textarea class="form-control" rows="3" name="hero_lead"
          placeholder="One-sentence description of what your platform does."><?=html_escape($s->hero_lead ?? '')?></textarea></div>
      </div>

      <hr>

      <!-- CTAs -->
      <h4>Call-to-action buttons</h4>
      <div class="form-group">
        <label class="col-md-3 control-label">Primary CTA label</label>
        <div class="col-md-6"><input class="form-control" name="cta_primary_label" maxlength="60"
          value="<?=html_escape($s->cta_primary_label ?? '')?>"
          placeholder="Sign your school up — free"></div>
      </div>
      <div class="form-group">
        <label class="col-md-3 control-label">Secondary CTA label</label>
        <div class="col-md-6"><input class="form-control" name="cta_secondary_label" maxlength="60"
          value="<?=html_escape($s->cta_secondary_label ?? '')?>"
          placeholder="See what is included"></div>
      </div>

      <hr>

      <!-- PRICING -->
      <h4>Pricing block</h4>
      <div class="form-group">
        <label class="col-md-3 control-label">Pricing mode</label>
        <div class="col-md-9">
          <?php $pm = $s->pricing_mode ?? 'free'; ?>
          <label class="radio-inline"><input type="radio" name="pricing_mode" value="free" <?=$pm==='free'?'checked':''?>>
            <strong>Free</strong> — show the ৳0 callout (today)</label>
          <label class="radio-inline"><input type="radio" name="pricing_mode" value="tiers" <?=$pm==='tiers'?'checked':''?>>
            <strong>Tiered</strong> — read <code>saas_package</code> (future)</label>
          <label class="radio-inline"><input type="radio" name="pricing_mode" value="hidden" <?=$pm==='hidden'?'checked':''?>>
            <strong>Hidden</strong> — no pricing section</label>
        </div>
      </div>
      <div class="form-group">
        <label class="col-md-3 control-label">Pricing headline</label>
        <div class="col-md-9"><input class="form-control" name="pricing_headline" maxlength="160"
          value="<?=html_escape($s->pricing_headline ?? '')?>"
          placeholder="One plan. Everything included."></div>
      </div>
      <div class="form-group">
        <label class="col-md-3 control-label">Future-plans note</label>
        <div class="col-md-9"><textarea class="form-control" rows="3" name="pricing_future_note"
          placeholder="Optional: explain that paid tiers are coming once server costs add up."><?=html_escape($s->pricing_future_note ?? '')?></textarea>
          <p class="help-block small">Shown as a callout under the price. Leave blank to hide.</p>
        </div>
      </div>

      <hr>

      <!-- SECTION TOGGLES -->
      <h4>Sections</h4>
      <p class="text-muted small">Hide any section without deleting its content.</p>
      <div class="form-group">
        <div class="col-md-9 col-md-offset-3">
          <label class="checkbox-inline"><input type="hidden" name="show_features" value="0">
            <input type="checkbox" name="show_features" value="1" <?=($s->show_features ?? 1) ? 'checked' : ''?>>
            Features grid</label>
          <label class="checkbox-inline" style="margin-left:18px"><input type="hidden" name="show_pricing" value="0">
            <input type="checkbox" name="show_pricing" value="1" <?=($s->show_pricing ?? 1) ? 'checked' : ''?>>
            Pricing block</label>
          <label class="checkbox-inline" style="margin-left:18px"><input type="hidden" name="show_testimonials" value="0">
            <input type="checkbox" name="show_testimonials" value="1" <?=($s->show_testimonials ?? 1) ? 'checked' : ''?>>
            Testimonials</label>
          <label class="checkbox-inline" style="margin-left:18px"><input type="hidden" name="show_schools" value="0">
            <input type="checkbox" name="show_schools" value="1" <?=($s->show_schools ?? 1) ? 'checked' : ''?>>
            Schools strip</label>
        </div>
      </div>

    </div>

    <div class="panel-footer">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save settings</button>
      <a target="_blank" href="<?=base_url('landing')?>" class="btn btn-default">Open live landing in new tab</a>
      <span class="pull-right text-muted small" style="line-height:34px">
        Last updated:
        <?=isset($s->updated_at) && $s->updated_at ? html_escape($s->updated_at) : '—'?>
      </span>
    </div>
  </form>
</section>

<style>
.panel-actions{float:right;margin-top:-2px}
.panel-actions .btn{margin-left:4px}
</style>
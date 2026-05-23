<?php
// Default the active template branch to the first branch_id that has a
// front_cms_setting row when nothing is configured yet, so the dropdown
// always opens on a sensible choice.
$activeTemplateId = (int) ($template_branch_id ?? 0);
?>
<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=translate('settings')?></h2>
</header>
<div class="panel-body">

  <h4 class="mb-md"><?=translate('template_tenant_for_new_subdomains') ?: 'Template tenant for new subdomains'?></h4>
  <p class="text-muted">
    <?=translate('template_tenant_for_new_subdomains_help') ?: 'When you approve a pending subdomain at <code>/saas/pending_request</code>, every branch-scoped <code>front_cms_*</code> row (theme, colours, logos, copyright, home-page layout, menu visibility, services list, …) is cloned from the branch you pick below onto the new tenant. Only the new tenant\'s identity fields (school name, url alias, email, mobile, address, copyright text) are rewritten — everything else is inherited verbatim.'?>
  </p>

  <?=form_open('saas/settings_general', ['class' => 'form-horizontal'])?>
    <input type="hidden" name="submit" value="save_template_branch">

    <div class="form-group">
      <label class="col-md-3 control-label"><?=translate('template_branch') ?: 'Template branch'?> *</label>
      <div class="col-md-6">
        <select name="template_branch_id" class="form-control" required>
          <option value=""><?=translate('please_select_a_branch') ?: '— please select a branch —'?></option>
          <?php foreach ($branches as $b): ?>
            <?php
              $hasCms    = !empty($b->cms_id);
              $label     = $b->name ?: ($b->school_name ?: ('Branch ' . $b->id));
              $subdomain = $b->subdomain ?: $b->slug;
              $disabled  = $hasCms ? '' : 'disabled';
              $selected  = ((int)$b->id === $activeTemplateId) ? 'selected' : '';
              $suffix    = $hasCms
                  ? ($b->cms_active ? '' : ' · cms_active=0')
                  : ' · ' . (translate('no_front_cms_setting_row') ?: 'no front_cms_setting row');
            ?>
            <option value="<?=$b->id?>" <?=$selected?> <?=$disabled?>>
              #<?=$b->id?> · <?=html_escape($label)?><?php if($subdomain): ?> · <?=html_escape($subdomain)?><?php endif; ?><?=html_escape($suffix)?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="help-block text-muted" style="margin-top:6px">
          <?=translate('template_branch_help') ?: 'Only branches that already have a <code>front_cms_setting</code> row are selectable. Open <code>/frontend/setting</code> on a branch first to seed one.'?>
        </p>
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-offset-3 col-md-6">
        <button class="btn btn-primary"><?=translate('save')?></button>
      </div>
    </div>
  <?=form_close()?>

  <hr>

  <h4 class="mt-lg mb-md"><?=translate('reclone_existing_tenants') ?: 'Re-clone existing tenants from template'?></h4>
  <p class="text-muted">
    <?=translate('reclone_existing_tenants_help') ?: 'Pushes the current template branch over an existing tenant — wipes their <code>front_cms_*</code> rows and re-clones them. Handy for tenants approved before the clone helper existed (they would otherwise still show the generic green placeholder) or for rolling out template changes to live subdomains. Identity fields (school name, email, phone) are inherited from the <code>branch</code> row so the cloned subdomain still announces itself correctly.'?>
  </p>

  <table class="table table-bordered table-hover table-condensed mb-none">
    <thead><tr>
      <th width="50">#</th>
      <th><?=translate('branch')?></th>
      <th><?=translate('subdomain')?></th>
      <th><?=translate('cms_setting') ?: 'CMS setting'?></th>
      <th width="180" class="no-sort"><?=translate('action')?></th>
    </tr></thead>
    <tbody>
      <?php foreach ($branches as $b): ?>
        <?php $isTemplate = ((int)$b->id === $activeTemplateId); ?>
        <tr<?=$isTemplate ? ' class="bg-info-light"' : ''?>>
          <td><?=$b->id?></td>
          <td>
            <?=html_escape($b->name ?: $b->school_name)?>
            <?php if ($isTemplate): ?>
              <span class="badge badge-info"><?=translate('template') ?: 'template'?></span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($b->subdomain): ?>
              <a href="https://<?=html_escape($b->subdomain)?>.smartschool.bd" target="_blank">
                <?=html_escape($b->subdomain)?>.smartschool.bd
              </a>
            <?php else: ?>—<?php endif; ?>
          </td>
          <td>
            <?php if (!empty($b->cms_id)): ?>
              <span class="badge badge-success"><?=translate('present') ?: 'present'?></span>
              <?php if (!$b->cms_active): ?>
                <span class="badge badge-warning"><?=translate('inactive') ?: 'inactive'?></span>
              <?php endif; ?>
            <?php else: ?>
              <span class="badge badge-default"><?=translate('missing') ?: 'missing'?></span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($isTemplate): ?>
              <span class="text-muted"><?=translate('current_template') ?: '(current template)'?></span>
            <?php else: ?>
              <?=form_open('saas/reclone_tenant/' . $b->id, [
                  'style'    => 'display:inline;margin:0;',
                  'onsubmit' => "return confirm('" . ((translate('reclone_confirm') ?: 'Re-clone this tenant\'s frontend from the template branch? This wipes every front_cms_* row for this tenant first.')) . "')"
              ])?>
                <button class="btn btn-warning btn-sm">
                  <i class="fas fa-clone"></i> <?=translate('reclone') ?: 'Re-clone'?>
                </button>
              <?=form_close()?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
</section>

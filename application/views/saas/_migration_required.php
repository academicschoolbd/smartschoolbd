<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title"><?=html_escape($page_title ?? 'Setup required');?></h2>
</header>
<div class="panel-body">
  <div class="alert alert-warning" style="font-size:15px;">
    <h4 style="margin-top:0;"><i class="fas fa-database"></i> One-time setup not yet applied</h4>
    <p>This page is missing something it needs to run:</p>
    <pre style="background:#f6f8fa;padding:10px;border-radius:6px;"><?=html_escape($reason ?? 'missing table: ' . ($missing_table ?? ''));?></pre>

    <p><strong>How to fix:</strong></p>
    <ol style="margin-top:14px;">
      <li>Upload all files from the latest deployment zip(s) to the same paths under <code>application/</code>.</li>
      <li>Open cPanel &rarr; phpMyAdmin &rarr; select <code>zgruhjabaz_smartschoolbd</code>.</li>
      <li>SQL tab &rarr; paste the contents of <a href="<?=html_escape($migration_url);?>" target="_blank"><?=html_escape($migration_file);?></a>.</li>
      <li>Click <strong>Go</strong>. The script is idempotent so it's safe to re-run.</li>
      <li>Reload this page.</li>
    </ol>
  </div>
</div>
</section>

<section class="panel">
<header class="panel-heading">
  <h2 class="panel-title">Notifications
    <small>out-of-band channels for SaaS billing events</small>
  </h2>
</header>
<?=form_open(base_url('saas/save_notifications'), ['class' => 'form-horizontal']);?>
<div class="panel-body">

<h4 style="margin-top:0"><i class="fab fa-telegram-plane" style="color:#0088cc"></i> Telegram bot</h4>
<p class="text-muted">Fires on: new SaaS invoice paid, new manual submission pending review, daily renewal cron summary.</p>

<div class="form-group">
  <label class="col-md-3 control-label">Enabled</label>
  <div class="col-md-9">
    <?php $enabled = $telegram && (int)$telegram->is_enabled === 1; ?>
    <label class="radio-inline"><input type="radio" name="telegram_enabled" value="1" <?=$enabled ? 'checked' : '';?>> Yes</label>
    <label class="radio-inline"><input type="radio" name="telegram_enabled" value="0" <?=!$enabled ? 'checked' : '';?>> No</label>
  </div>
</div>

<div class="form-group">
  <label class="col-md-3 control-label">Bot token <span class="text-danger">*</span></label>
  <div class="col-md-9">
    <input type="text" name="bot_token" class="form-control" autocomplete="off"
      value="<?=html_escape($telegram_config['bot_token'] ?? '');?>"
      placeholder="123456789:AAH...">
    <p class="help-block">Create a bot via <a href="https://t.me/BotFather" target="_blank">@BotFather</a> on Telegram → <code>/newbot</code> → copy the HTTP API token.</p>
  </div>
</div>

<div class="form-group">
  <label class="col-md-3 control-label">Admin chat ID <span class="text-danger">*</span></label>
  <div class="col-md-9">
    <input type="text" name="admin_chat_id" class="form-control"
      value="<?=html_escape($telegram_config['admin_chat_id'] ?? '');?>"
      placeholder="e.g. 123456789 or -1001234567890">
    <p class="help-block">
      For a direct message: DM your new bot once, then visit
      <code>https://api.telegram.org/bot&lt;TOKEN&gt;/getUpdates</code> in a browser; the chat id is in <code>result[0].message.chat.id</code>.
      For a group: add the bot, send a message in the group, then read the same URL.
    </p>
  </div>
</div>

</div>
<footer class="panel-footer text-right">
  <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
</footer>
<?=form_close();?>

<?=form_open(base_url('saas/test_telegram'), ['style' => 'padding: 0 18px 18px 18px;']);?>
  <button type="submit" class="btn btn-default">
    <i class="fas fa-paper-plane"></i> Send test message to admin chat
  </button>
  <span class="text-muted small">Use this to confirm your token + chat id work before going live.</span>
<?=form_close();?>
</section>

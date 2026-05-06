<div class="pageoverflow">
  <img src="{$mas_nc_banner_url|escape:'html'}" alt="" width="600" height="120" style="max-width:100%;height:auto;border-radius:6px;margin-bottom:12px;">
  <h2>{$mod->Lang('friendlyname')|escape:'html'} <span style="font-weight:normal;font-size:12px;">v{$mas_nc_version|escape}</span></h2>
  <p class="pagetext">{$mod->Lang('moddescription')}</p>
</div>

{$settings_form_start}
<input type="hidden" name="{$actionid}active_tab" value="settings">

<h3 class="pagetext">{$mod->Lang('section_channels')}</h3>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('enable_email')}</p>
  <p class="pageinput"><label><input type="checkbox" name="{$actionid}mas_nc_enable_email" value="1" {if $enable_email_checked == '1'}checked{/if}> {$mod->Lang('enable_email_help')}</label></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('email_recipients')}</p>
  <p class="pageinput"><input type="text" name="{$actionid}mas_nc_email_recipients" value="{$email_recipients|escape}" style="width:90%;max-width:560px;" autocomplete="off" placeholder="admin@example.com"></p>
  <p class="pageinput"><span class="information">{$mod->Lang('email_recipients_help')}</span></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('enable_discord')}</p>
  <p class="pageinput"><label><input type="checkbox" name="{$actionid}mas_nc_enable_discord" value="1" {if $enable_discord_checked == '1'}checked{/if}> {$mod->Lang('enable_discord_help')}</label></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('discord_webhook')}</p>
  <p class="pageinput"><input type="text" name="{$actionid}mas_nc_discord_webhook" value="{$discord_webhook|escape}" style="width:90%;max-width:560px;" autocomplete="off" spellcheck="false"></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('enable_push')}</p>
  <p class="pageinput"><label><input type="checkbox" name="{$actionid}mas_nc_enable_push" value="1" {if $enable_push_checked == '1'}checked{/if}> {$mod->Lang('enable_push_help')}</label></p>
</div>

<h3 class="pagetext">{$mod->Lang('section_webpush')}</h3>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('vapid_public')}</p>
  <p class="pageinput"><code style="word-break:break-all;">{$mas_nc_vapid_public|escape}</code></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('vapid_contact')}</p>
  <p class="pageinput"><input type="text" name="{$actionid}mas_nc_vapid_contact" value="{$vapid_contact|escape}" style="width:90%;max-width:560px;"></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('manifest_url')}</p>
  <p class="pageinput"><a href="{$mas_nc_manifest_url|escape:'html'}" target="_blank" rel="noopener noreferrer">{$mas_nc_manifest_url|escape:'html'}</a></p>
</div>
<div class="pageoverflow">
  <p class="information">{$mod->Lang('webpush_install_note')}</p>
</div>
<div class="pageoverflow">
  <p class="pageinput">
    <button type="button" class="pagebutton" id="mas-nc-subscribe-btn">{$mod->Lang('btn_subscribe_push')}</button>
    <input type="submit" name="{$actionid}mas_nc_send_test_push" class="pagebutton" value="{$mod->Lang('btn_test_push')}">
    <input type="submit" name="{$actionid}mas_nc_rotate_vapid" class="pagebutton" value="{$mod->Lang('btn_rotate_vapid')}" onclick="return confirm('Rotate VAPID keys and remove push subscriptions?');">
    <input type="submit" name="{$actionid}mas_nc_publish_sw" class="pagebutton" value="{$mod->Lang('btn_publish_sw')}">
  </p>
  <p class="pageinput"><span class="information">{$mod->Lang('publish_sw_help')}</span></p>
</div>
<div id="mas-nc-push-config"
     data-subscribe-url="{$mas_nc_ajax_push_subscribe_url|escape:'html'}"
     data-vapid-public="{$mas_nc_vapid_public|escape:'html'}"
     data-sw-url="{$mas_nc_sw_path|escape:'html'}"></div>

<h3 class="pagetext">{$mod->Lang('section_events')}</h3>
<div class="pageoverflow">
  <p class="pageinput"><label><input type="checkbox" name="{$actionid}mas_nc_evt_login_failed" value="1" {if $evt_login_failed_checked == '1'}checked{/if}> {$mod->Lang('evt_login_failed_opt')}</label></p>
  <p class="pageinput"><label><input type="checkbox" name="{$actionid}mas_nc_evt_delete_user" value="1" {if $evt_delete_user_checked == '1'}checked{/if}> {$mod->Lang('evt_delete_user_opt')}</label></p>
</div>

<h3 class="pagetext">{$mod->Lang('section_rates')}</h3>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('rate_per_hour')}</p>
  <p class="pageinput"><input type="number" name="{$actionid}mas_nc_rate_per_hour" value="{$rate_per_hour|escape}" min="1" max="10000" style="width:120px;"></p>
</div>

<h3 class="pagetext">{$mod->Lang('section_health')}</h3>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('health_min_php')}</p>
  <p class="pageinput"><input type="text" name="{$actionid}mas_nc_health_min_php" value="{$health_min_php|escape}" style="width:120px;"></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('health_disk_pct')}</p>
  <p class="pageinput"><input type="number" name="{$actionid}mas_nc_health_disk_pct" value="{$health_disk_pct|escape}" min="1" max="99" style="width:100px;"></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('health_db_ms')}</p>
  <p class="pageinput"><input type="number" name="{$actionid}mas_nc_health_db_ms" value="{$health_db_ms|escape}" min="0" max="60000" style="width:120px;"></p>
</div>

<h3 class="pagetext">{$mod->Lang('section_advanced')}</h3>
<div class="pageoverflow">
  <p class="pageinput"><label><input type="checkbox" name="{$actionid}mas_nc_fatal_shutdown" value="1" {if $fatal_shutdown_checked == '1'}checked{/if}> {$mod->Lang('fatal_shutdown_opt')}</label></p>
  <p class="pageinput"><span class="information">{$mod->Lang('fatal_shutdown_warn')}</span></p>
</div>

<h3 class="pagetext">{$mod->Lang('section_cron')}</h3>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('cron_url_label')}</p>
  <p class="pageinput"><code style="word-break:break-all;">{$mas_nc_cron_url|escape}</code></p>
  <p class="pageinput"><span class="information">{$mod->Lang('cron_help')}</span></p>
</div>

<h3 class="pagetext">{$mod->Lang('show_donations_tab')}</h3>
<div class="pageoverflow">
  <p class="pageinput"><label><input type="checkbox" name="{$actionid}show_donations_tab_settings" value="1" {if $show_donations_tab_checked == '1'}checked{/if}> {$mod->Lang('show_donations_tab_help')}</label></p>
</div>

<div class="pageoverflow">
  <p class="pageinput"><input type="submit" class="pagebutton" value="{$mod->Lang('save')}"></p>
</div>

{$settings_form_end}

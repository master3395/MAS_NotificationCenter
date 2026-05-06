<div class="pageoverflow">
  <h2>{$mod->Lang('donations_tab')|escape:'html'}</h2>

  <div class="pageoverflow" style="border:1px solid #cfd7df;padding:12px;border-radius:3px;background:#fff;max-width:850px;">
    <h3 class="pagetext">{$mod->Lang('sponsors')}</h3>
    <div style="display:flex;align-items:center;gap:12px;margin-top:10px;flex-wrap:wrap;">
      <img
        src="{$donations_sponsor_logo_url|escape:'html'}"
        alt="{$donations_sponsor_logo_alt|escape:'html'}"
        width="48"
        height="48"
        style="max-width:48px;max-height:48px;width:auto;height:auto;object-fit:contain;flex-shrink:0;"
        loading="lazy"
        decoding="async"
      >
      <a href="{$donations_sponsor_href|escape:'html'}" target="_blank" rel="noopener noreferrer">{$donations_sponsor_link|escape}</a>
    </div>
  </div>

  <div class="pageoverflow" style="margin-top:20px;">
    <p class="pagetext">{$donationstext}</p>
    <p class="pageinput"><a class="pagebutton" style="display:inline-block;background:#ffc439;color:#000;padding:8px 14px;border-radius:4px;text-decoration:none;" href="https://newstargeted.com/" target="_blank" rel="noopener noreferrer">{$mod->Lang('donate_btn')}</a></p>
  </div>

  {$settings_form_start}
  <input type="hidden" name="{$actionid}active_tab" value="donations">
  <input type="hidden" name="{$actionid}show_donations_tab" value="0">
  <div class="pageoverflow" style="margin-top:16px;">
    <p class="pageinput">{$hidedonationssubmit}</p>
  </div>
  {$settings_form_end}
</div>

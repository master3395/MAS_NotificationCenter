<div class="pageoverflow" style="margin-top:12px;">
  <h3>{$mod->Lang('about_heading_module')|escape:'html'}</h3>
  <p><strong>{$mod->Lang('about_label_name')|escape:'html'}</strong> {$mod->GetFriendlyName()|escape:'html'}</p>
  <p><strong>{$mod->Lang('about_label_version')|escape:'html'}</strong> {$mod->GetVersion()|escape:'html'}</p>
  <p><strong>{$mod->Lang('about_label_author')|escape:'html'}</strong> {$mod->GetAuthor()|escape:'html'}</p>
  <p><strong>{$mod->Lang('about_label_email')|escape:'html'}</strong> <a href="mailto:{$mod->GetAuthorEmail()|escape:'url'}">{$mod->GetAuthorEmail()|escape:'html'}</a></p>
  <p><strong>{$mod->Lang('about_label_license')|escape:'html'}</strong> {$mod->Lang('about_license_value')|escape:'html'}</p>
</div>

<div class="pageoverflow">
  <h3>{$mod->Lang('about_heading_compat')|escape:'html'}</h3>
  <p>{$mod->Lang('about_min_cms')|escape:'html'} {$mod->MinimumCMSVersion()|escape:'html'}</p>
  <p>{$mod->Lang('about_min_php')|escape:'html'} {$mod->GetMinimumPHPVersion()|escape:'html'}</p>
</div>

<div class="pageoverflow">
  <h3>{$mod->Lang('about_heading_summary')|escape:'html'}</h3>
  <p>{$mod->Lang('moddescription')|escape:'html'}</p>
</div>

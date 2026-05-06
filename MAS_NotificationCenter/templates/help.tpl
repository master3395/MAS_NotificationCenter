<div class="clear"></div>

<div class="information" style="margin-top: 20px; padding: 15px; background: #e8f4f8; border-left: 4px solid #2196F3;">
  <p style="margin: 0;"><strong>{$mod->Lang('help_banner_title')|escape:'html'}</strong> {$mod->Lang('help_banner_body')|escape:'html'} <a href="https://newstargeted.com/" target="_blank" rel="noopener noreferrer">newstargeted.com</a></p>
</div>

{* Inner navigation must NOT use div#page_tabs: cms_initTabs() binds ALL divs under the real #page_tabs and would hide #help_c when clicking sub-tabs. Use <a role="tab"> plus scoped script. *}
<style type="text/css">
#mas_nc_help_subtabs {
  overflow: hidden;
  margin-top: 20px;
}
#mas_nc_help_subtabs a.mas-nc-help-tab {
  color: #585858;
  border-radius: 4px 4px 0 0;
  text-shadow: -1px 1px 0 #fff;
  background: #ddd;
  font-weight: bold;
  text-align: center;
  float: left;
  cursor: pointer;
  white-space: nowrap;
  padding: 11px;
  margin-top: 1px;
  margin-right: 1px;
  text-decoration: none;
  display: block;
  box-sizing: border-box;
}
#mas_nc_help_subtabs a.mas-nc-help-tab:hover,
#mas_nc_help_subtabs a.mas-nc-help-tab:focus {
  color: #147fdb;
}
#mas_nc_help_subtabs a.mas-nc-help-tab.active {
  color: #fff;
  text-shadow: -1px -1px 0 #054882;
  background: #147fdb;
}
#mas_nc_help_subcontent {
  background: #fff;
  border-radius: 0 0 4px 4px;
  padding: 20px 10px;
  border: 1px solid #ddd;
  border-top: 3px solid #147fdb;
  clear: both;
}
</style>

<nav id="mas_nc_help_subtabs" aria-label="Help sections">
  <a href="#" role="tab" class="mas-nc-help-tab active" id="mas_nc_help_tab_general">{$mod->Lang('help_tab_general')|escape:'html'}</a>
  <a href="#" role="tab" class="mas-nc-help-tab" id="mas_nc_help_tab_channels">{$mod->Lang('help_tab_channels')|escape:'html'}</a>
  <a href="#" role="tab" class="mas-nc-help-tab" id="mas_nc_help_tab_interop">{$mod->Lang('help_tab_interop')|escape:'html'}</a>
</nav>

<div class="clearb"></div>
<div id="mas_nc_help_subcontent">

  <section id="mas_nc_help_panel_general" class="mas-nc-help-panel" role="tabpanel">
    {include file='module_file_tpl:MAS_NotificationCenter;help_general_tab.tpl'}
  </section>
  <section id="mas_nc_help_panel_channels" class="mas-nc-help-panel" style="display:none;" role="tabpanel">
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('help_channels_body')}</p>
    </div>
  </section>
  <section id="mas_nc_help_panel_interop" class="mas-nc-help-panel" style="display:none;" role="tabpanel">
    <div class="pageoverflow">
      <p class="pagetext">{$mod->Lang('help_interop_body')}</p>
    </div>
  </section>

  <div class="clearb"></div>
</div>

<script type="text/javascript">
(function () {
  if (typeof jQuery === 'undefined') {
    return;
  }
  jQuery(function ($) {
    var $tabs = $('#mas_nc_help_subtabs a.mas-nc-help-tab');
    var $panels = $('#mas_nc_help_subcontent section.mas-nc-help-panel');
    if (!$tabs.length || !$panels.length) {
      return;
    }
    function activate($tab) {
      var suffix = $tab.attr('id').replace(/^mas_nc_help_tab_/, '');
      $tabs.removeClass('active');
      $tab.addClass('active');
      $panels.hide();
      $('#mas_nc_help_panel_' + suffix).show();
    }
    $tabs.on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      activate($(this));
      return false;
    });
    var $first = $tabs.filter('.active').first();
    if ($first.length) {
      activate($first);
    } else {
      activate($tabs.first());
    }
  });
})();
</script>

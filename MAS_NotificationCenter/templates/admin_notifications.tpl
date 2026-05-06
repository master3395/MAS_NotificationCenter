<div class="pageoverflow">
  <h2>{$mod->Lang('tab_notifications')|escape:'html'}</h2>
  <p class="information">{$mod->Lang('notifications_intro')}</p>
</div>

{$settings_form_start}
<input type="hidden" name="{$actionid}active_tab" value="notifications">

{if isset($mas_nc_notifications) && $mas_nc_notifications|@count > 0}
<table class="pagetable" style="width:100%;border-collapse:collapse;">
  <thead>
    <tr>
      <th></th>
      <th>{$mod->Lang('col_created')}</th>
      <th>{$mod->Lang('col_severity')}</th>
      <th>{$mod->Lang('col_category')}</th>
      <th>{$mod->Lang('col_title')}</th>
      <th>{$mod->Lang('col_delivered')}</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$mas_nc_notifications item=row}
    <tr>
      <td><input type="checkbox" name="{$actionid}mas_nc_sel[]" value="{$row.id|escape}"></td>
      <td>{$row.created_fmt|escape}</td>
      <td>{$row.severity|escape}</td>
      <td>{$row.category|escape}</td>
      <td><strong>{$row.title|escape}</strong><br><small>{$row.body|escape}</small></td>
      <td>{$row.delivered_mask|escape}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
<div class="pageoverflow" style="margin-top:12px;">
  <p class="pageinput">
    <input type="submit" name="{$actionid}mas_nc_delete_selected" class="pagebutton" value="{$mod->Lang('btn_delete_selected')}" onclick="return confirm('{$mod->Lang('confirm_delete')|escape:'javascript'}');">
  </p>
</div>
{else}
<p class="pagetext">{$mod->Lang('notifications_empty')}</p>
{/if}

<div class="pageoverflow" style="margin-top:16px;">
  <p class="pagetext">
    {if $mas_nc_has_prev && $mas_nc_prev_url != ''}
      <a href="{$mas_nc_prev_url|escape:'html'}">{$mod->Lang('pager_prev')}</a>
    {/if}
    &nbsp; {$mod->Lang('pager_page')} {$mas_nc_page|escape} — {$mod->Lang('pager_total')} {$mas_nc_total|escape} &nbsp;
    {if $mas_nc_has_next && $mas_nc_next_url != ''}
      <a href="{$mas_nc_next_url|escape:'html'}">{$mod->Lang('pager_next')}</a>
    {/if}
  </p>
</div>

{$settings_form_end}

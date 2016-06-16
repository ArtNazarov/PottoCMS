<script>
function new_opt()
{
	document.forms.opts.mod_action.value = "fillopt";
	document.forms.opts.optname.value = '';
	document.forms.opts.submit();
}
function edit_opt(optname)
{
	document.forms.opts.mod_action.value = "filledit";
	document.forms.opts.optname.value = optname;
	document.forms.opts.submit();
}
function delete_opt(optname)
{
	document.forms.opts.mod_action.value = "deleteopt";
	document.forms.opts.optname.value = optname;
	document.forms.opts.submit();
}
</script>
<form name="opts" action="/admin/reestr" style="display:hidden" method="POST" >
<input type="hidden" name="mod_action" value="">
<input type="hidden" name="optname" value="">
</form>
<h3>Реестр настроек (Единый журнал настроек)</h3>
<span class="btn" style="cursor:pointer" onclick="javascript:new_opt();">Внести новую настройку</span>
<hr/>
<table class="cp" width="100%">
<tr>
   <td>ID настройки</td>
   <td>Описание настройки</td>
   <td>Значение настройки</td>
   <td>Действия</td>
</tr>
~ITEMS~
</table>
<script>
function new_permission()
{
	document.forms.permissions.mod_action.value = "fillnew";
	document.forms.permissions.pmodule.value = '';
	document.forms.permissions.paction.value = '';
	document.forms.permissions.paccess.value = '';
	document.forms.permissions.submit();
}
function edit_permission(pmodule, paction, paccess, proles)
{
	document.forms.permissions.mod_action.value = "filledit";
	document.forms.permissions.pmodule.value = pmodule;
    document.forms.permissions.paction.value = paction;
	document.forms.permissions.paccess.value = paccess;
	document.forms.permissions.proles.value = proles;
	document.forms.permissions.submit();
}
function delete_permission(pmodule, paction, paccess)
{
	document.forms.permissions.mod_action.value = "delete";
	document.forms.permissions.pmodule.value = pmodule;
    document.forms.permissions.paction.value = paction;
	document.forms.permissions.paccess.value = paccess;
	document.forms.permissions.submit();
}
</script>
<form name="permissions" action="/admin/permissions" style="display:hidden" method="POST" >
<input type="hidden" name="mod_action" value="">
<input type="hidden" name="pmodule" value="">
<input type="hidden" name="paction" value="">
<input type="hidden" name="proles" value="">
<input type="hidden" name="paccess" value="">
</form>
<h3>Правила безопасности</h3>
<span class="btn" style="cursor:pointer" onclick="javascript:new_permission();">Создать правило безопасности</span>
<hr/>
<table class="cp" width="100%">
<tr><td>Модуль</td>
   <td>Действие</td>
  <td>Уровень доступа</td>
  <td>Группы пользователей</td>
  <td>Действия</td>
</tr>
~ITEMS~
</table>
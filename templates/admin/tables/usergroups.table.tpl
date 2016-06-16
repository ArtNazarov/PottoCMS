<script>
function new_role()
{
	document.forms.usergroups.mod_action.value = "fillrole";
	document.forms.usergroups.role.value = '';
	document.forms.usergroups.submit();
}
function edit_role(role)
{
	document.forms.usergroups.mod_action.value = "filledit";
	document.forms.usergroups.role.value = role;
	document.forms.usergroups.submit();
}
function delete_role(role)
{
	document.forms.usergroups.mod_action.value = "deleterole";
	document.forms.usergroups.role.value = role;
	document.forms.usergroups.submit();
}
</script>
<form name="usergroups" action="/admin/usergroups" style="display:hidden" method="POST" >
<input type="hidden" name="mod_action" value="">
<input type="hidden" name="role" value="">
</form>
<h3>Группы пользователей</h3>
<span class="btn" style="cursor:pointer" onclick="javascript:new_role();">Добавить новую роль</span>
<hr/>
<table class="cp" width="100%">
<tr><td>Группа</td>
   <td>Описание</td>
  <td>Уровень доступа</td>
  <td>Действия</td>
</tr>
~ITEMS~
</table>
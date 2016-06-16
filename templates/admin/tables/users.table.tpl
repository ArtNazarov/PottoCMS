<script type="text/javascript">
function user_role(user)
{
document.users.mod_action.value = "userrole";
document.users.user.value = user;
document.forms['users'].submit();
}
function user_delete(user)
{
document.users.mod_action.value = "userdelete";
document.users.user.value = user;
document.forms['users'].submit();
}
</script>
<form name="users" style="display:hidden" method="POST" action="/admin/users">
<input type="hidden" name="module" value="categories">
<input type="hidden" name="mod_action" value="">
<input type="hidden" name="user" value="">
</form>
<h3>Список пользователей Вашего сайта</h3><br/>
<table class="cp" width="100%" border="1" cellpadding="5" cellspacing="5">
<tr>
<td valign="top" align="left">Имя пользователя</td>
<td valign="top" align="left">Ключ</td>
<td valign="top" align="left">Действия</tr>
~ITEMS~
</table>

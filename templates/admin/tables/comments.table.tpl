<script type="text/javascript">
function delete_comment(id_com)
{
document.comments.mod_action.value = "delete";
document.comments.id_comment.value = id_com;
document.forms['comments'].submit();
}
function edit_comment(id_com)
{
document.comments.mod_action.value = "edit";
document.comments.id_comment.value = id_com;
document.forms['comments'].submit();
}
function accept_comment(id_com)
{
document.comments.mod_action.value = "accept";
document.comments.id_comment.value = id_com;
document.forms['comments'].submit();
}
function decline_comment(id_com)
{
document.comments.mod_action.value = "decline";
document.comments.id_comment.value = id_com;
document.forms['comments'].submit();
}
</script>
<form name="comments" style="display:none" method="POST" action="/admin/comments">
<input type="hidden" name="id_comment">
<input type="hidden" name="mod_action">
</form>
<h3>Список комментариев</h3>
<table class="cp" width="100%" border="1"><tr>
<td valign="top" align="left">ID комментария</td>
<td valign="top" align="left">Имя пользователя</td>
<td valign="top" align="left">Создано</td>
<td valign="top" align="left">ID cтраницы</td>
<td valign="top" align="left">Заголовок</td>
<td valign="top" align="left">Рейтинг</td>
<td valign="top" align="left">Действия</td></tr>
~ITEMS~
</table>

<script type="text/javascript">
function delete_category(category)
{
document.forms.categories.mod_action.value = "delcat";
document.forms.categories.category.value = category;
document.forms.categories.submit();
}
function edit_category(category)
{
document.forms.categories.mod_action.value = "fillupdatecat";
document.forms.categories.category.value = category;
document.forms.categories.submit();
}
function new_category()
{
	document.forms.categories.mod_action.value = "filladdcat";
	document.forms.categories.category.value = '';
	document.forms.categories.submit();
}
</script>
~SYSTEM_MESSAGE~
<form name="categories" style="display:hidden" method="POST" action="/admin/categories">
<input type="hidden" name="mod_action" value="">
<input type="hidden" name="category" value="">
</form>
<span class="btn" style="cursor:pointer" onclick="javascript:new_category();">Добавить новую категорию</span>
<hr/>
<table class="cp" width="100%" border="1" cellpadding="5" cellspacing="5">
<tr>
<td valign="top" align="left">Категория</td>
<td valign="top" align="left">Предок</td>
<td valign="top" align="left">Заголовок</td>
<td valign="top" align="left">Действия</td></tr>
~ITEMS~
</table>
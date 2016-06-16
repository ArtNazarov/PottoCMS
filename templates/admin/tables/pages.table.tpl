<form name="pages" style="display:hidden" method="POST" action="/admin/pages">
<input type="hidden" name="mod_action">
<input type="hidden" name="id">
<input type="hidden" name="page">
<input type="hidden" name="ordering" value="~VALUE_ORDERING~">
<input type="hidden" name="filter_status" value="~VALUE_FSTATUS~">
<input type="hidden" name="filter_category" value="~VALUE_FCATEGORY~">
</form>
<script type="text/javascript">
function clear_bin()
{
	document.pages.mod_action.value = "clearbin";
	document.forms['pages'].submit();
}
function edit_page(id)
{
document.pages.mod_action.value = "editpage";
document.pages.id.value = id;
document.forms['pages'].submit();;
}
function delete_page(id)
{
document.pages.mod_action.value = "delpage";
document.pages.id.value = id;
document.forms['pages'].submit();
}
function new_page()
{
	document.pages.mod_action.value = "fillpage";
	document.forms['pages'].submit();
}
function gtpage(num)
{
	document.pages.mod_action.value = "listpages";
	document.pages.page.value = num;
	document.forms['pages'].submit();
}
</script>
<span class="btn" style="padding:5px;cursor:pointer" onclick="javascript:new_page();">Новая страница</span> <span class="btn" style="padding:5px;cursor:pointer" onclick="javascript:clear_bin();">Очистить корзину</span>
<hr/>
<form name="ui" id="ui"> Сортировка: ~ORDERING~ , фильтр - статус: ~FILTER_STATUS~ , категория: ~FILTER_CATEGORY~
</form>
<hr/>
<table class="cp" width="100%" border="1" cellpadding="5" cellspacing="5">
<tr>
<td valign="top" align="left">Кат.</td>
<td valign="top" align="left">ID</td>
<td valign="top" align="left">Заголовок</td>
<td valign="top" align="left">Создано</td>
<td valign="top" align="left">Пользователь</td>
<td valign="top" align="left">Статус</td>
<td valign="top" align="left">Просмотров</td>
<td valign="top" align="left">Действия</td></tr>
~ITEMS~
</table>
<div style="margin:20px; background-color:#e8e8e8">
~PAGINATOR~
</div>
<div style="margin:20px; background-color:#e8e8e8">
Выбрано по фильтру ~TOTALPAGES~ страниц по ~PERPAGES~ записей
</div>

<script language="javascript">
function change_filter_status()
{
document.pages.filter_status.value =
document.ui.filter_status_ui.value;

document.forms['pages'].submit();
}
function change_filter_category()
{
document.pages.filter_category.value =
document.ui.filter_category_ui.value;

document.forms['pages'].submit();
}
function change_ordering()
{
document.pages.ordering.value =
document.ui.ordering_ui.value;

document.forms['pages'].submit();
}
document.getElementById('ordering_ui').onchange = function () {change_ordering()};
document.getElementById('filter_category_ui').onchange = function () {change_filter_category();};
document.getElementById('filter_status_ui').onchange = function () {change_filter_status();};
</script>
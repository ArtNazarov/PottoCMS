<script type="text/javascript">
function delete_style(stylename)
{
document.styles.mod_action.value = "delete";
document.styles.stylename.value = stylename;
document.forms['styles'].submit();
}
function edit_style(stylename)
{
document.styles.mod_action.value = "filledit";
document.styles.stylename.value = stylename;
document.forms['styles'].submit();
}
function new_style(stylename)
{
document.styles.mod_action.value = "fillnew";
document.styles.stylename.value = stylename;
document.forms['styles'].submit();
}
</script>
<form name="styles" style="display:none" method="POST" action="/admin/styles">
<input type="hidden" name="stylename">
<input type="hidden" name="mod_action">
<input type="hidden" name="do" value='styles'>
</form>
<h3>Список стилей</h3>
<span class="btn" style="padding:5px;cursor:pointer" onclick="javascript:new_style();">Новый стиль</span> 
<hr/>
<table class="cp" width="100%" border="1"><tr>
<td valign="top" align="left">Переменная для стиля</td>
<td valign="top" align="left">Пояснение к стилю</td>
<td valign="top" align="left">Инструкции CSS</td>
<td valign="top" align="left">Действия</td></tr>
~ITEMS~
</table>
<hr/>
<p class='note'>При добавлении нового стиля необходимо разместить код переменной стиля в шаблоне вручную. Для этого
воспользуйтесь файловым менеджером CMS. Стиль предоставляется также по "/style.php?stylename=имя стиля"
Чтобы в шаблоне *.tpl были доступны данные стили, используйте в секции HEAD переменную SYS_STYLES
</p>
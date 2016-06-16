<script type="text/javascript">
function delete_block(blockname)
{
document.blocks.mod_action.value = "delete";
document.blocks.blockname.value = blockname;
document.forms['blocks'].submit();
}
function edit_block(blockname)
{
document.blocks.mod_action.value = "filledit";
document.blocks.blockname.value = blockname;
document.forms['blocks'].submit();
}
function new_block(blockname)
{
document.blocks.mod_action.value = "fillnew";
document.blocks.blockname.value = blockname;
document.forms['blocks'].submit();
}
</script>
<form name="blocks" style="display:none" method="POST" action="/admin/blocks">
<input type="hidden" name="blockname">
<input type="hidden" name="mod_action">
<input type="hidden" name="do" value='blocks'>
</form>
<h3>Список блоков</h3>
<span class="btn" style="padding:5px;cursor:pointer" onclick="javascript:new_block();">Новый блок</span> 
<hr/>
<table class="cp" width="100%" border="1"><tr>
<td valign="top" align="left">Переменная блока для шаблона</td>
<td valign="top" align="left">Пояснение к блоку</td>
<td valign="top" align="left">Текст для сайта</td>
<td valign="top" align="left">Действия</td></tr>
~ITEMS~
</table>
<hr/>
<p>При добавлении нового блока необходимо разместить код переменной блока в шаблоне вручную. Для этого
воспользуйтесь файловым менеджером CMS</p>
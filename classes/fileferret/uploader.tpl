Загрузить файл в ~DIROBJECT~
<form id="fmjs" name="fmjs" enctype="multipart/form-data" action="/admin/filemanager" method="post">
<input type="submit" value="Начать загрузку" /> <input type="button"  value="Добавить файл..." onclick="addfield()" /><br/>
<input type="hidden" name="action" value="~ACTION~" />
<input type="hidden" name="dirobject" value="~DIROBJECT~" />
<input type="hidden" name="selobjects" value="~SELOBJECTS~" />
<input type="hidden" name="clipdir" value="~CLIPDIR~" />
<input type="hidden" name="clipsel" value="~CLIPSEL~" />
<input type="hidden" name="clipflag" value="~CLIPFLAG~" />

</form>

<script language="javascript">
function addfield()
{
newItem = document.createElement("input");
newItem.type = "file";
newItem.name = "fupload[]";
newItem.value = "";
form = document.forms.fmjs;
form.appendChild(newItem);
 };
</script>
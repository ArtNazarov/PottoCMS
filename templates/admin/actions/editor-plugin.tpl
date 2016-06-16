
<form name="editor" action="/special/save/~PAGE-ID~" method="post">
<input type="hidden"  name="mod_action" value="~ACTION~" />
<input name="id_old" type="hidden" id="id_old" value="~PAGE-ID~"/>
  <table width="100%" border="0">
    <tr>
      <td align="left" valign="top">   <label for="id">Заголовок страницы </label>
        <input name="title" type="text" id="title" value="~PAGE-TITLE~" maxlength="20" />
    </td>
      <td align="right" valign="top">
          <label for="id">ID страницы </label><br />
      <input name="id" type="text" id="id" value="~PAGE-ID~" maxlength="20" /></td>
    </tr>
    <tr>
      <td colspan="2" align="left" valign="top">
          <label for="body">Текст страницы<br />
          </label>
      <textarea name="body" id="body" cols="80" rows="15">~PAGE-BODY~</textarea></td>
    </tr>
    <tr>
      <td colspan="2" align="left" valign="top">
        <label for="category">Категория материала</label>
        <br/>~CAT~<br/>
      </td>
      <tr>
      <td colspan="2" align="left" valign="top">
        <label for="status">Статус материала</label>
        <br/>~STATUS~<br/>
      </td>
    </tr>
      <tr>
        <td colspan="2" align="left" valign="top">
    <label for="created">Создано</label><br />
    <input name="created" type="text" id="created" value="~CREATED~" maxlength="20" />

</td>
      </tr>
        <tr>
        <td colspan="2" align="left" valign="top">
    <label for="username">Автор</label><br />
    <input name="username" type="text" id="username" value="~USERNAME~" maxlength="20" />

</td>
      </tr>
  </table>
    <input type="hidden" name="filter_category" value="">
    <input class="btn" type="submit" name="button" id="button" value="Применить">
</form>
<script type="text/javascript" src="/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">

document.forms.editor.filter_category.value = document.forms.editor.category.value;

document.getElementById("category").onchange = function()
 { 	document.forms.editor.filter_category.value = document.forms.editor.category.value; };
CKEDITOR.replace( 'body');
</script>
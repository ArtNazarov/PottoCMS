<form action="/admin/categories/" method="post">
<input type="hidden"  name="mod_action" value="~ACTION~" />
  <table width="100%" border="0">
  <tr>
    <td><label for="category">ID категории</label></td>
    <td><input name="category" type="text" id="category" value="~CATEGORY~" maxlength="20" /></td>
  </tr>
  <tr>
    <td><label for="parent">Принадлежит разделу</label></td>
    <td>~PARENT~</td>
  </tr>
  <tr>
    <td><label for="cat_name">Название категории</label></td>
    <td>  <input name="cat_name" type="text" id="cat_name" value="~CAT_NAME~" maxlength="20" /></td>
  </tr>
   <tr>
    <td><label for="template">Адрес шаблона</label></td>
    <td>  <input name="template" type="text" id="template" value="~TEMPLATE~" maxlength="600" /></td>
  </tr>
</table>
<input type="hidden" value="~CATEGORY~" name="old_category" id="old_category" />
<input class="btn" type="submit" name="button" id="button" value="Применить">
</form>


<form action="/admin/usergroups/" method="post">
<input type="hidden"  name="mod_action" value="~ACTION~" />
  <table width="100%" border="0">
  <tr>
    <td><label for="role">Группа</label></td>
    <td><input name="role" type="text" id="role" value="~ROLE~" maxlength="20" /></td>
  </tr>
  <tr>
    <td><label for="parent">Описание</label></td>
     <td><input name="rolename" type="text" id="rolename" value="~ROLENAME~" maxlength="100" /></td>
  </tr>
  <tr>
    <td><label for="access">Уровень доступа</label></td>
    <td>  <input name="access" type="text" id="access" value="~ACCESS~" maxlength="20" /></td>
  </tr>
</table>
<input type="hidden" value="~ROLE~" name="old_role" id="old_role" />
<input class="btn" type="submit" name="button" id="button" value="Применить">
</form>


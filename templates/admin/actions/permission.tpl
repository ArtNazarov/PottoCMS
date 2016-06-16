<form action="/admin/permissions/" method="post">
<input type="hidden"  name="mod_action" value="~ACTION~" />
  <table width="100%" border="0">
  <tr>
    <td valign="top"><label for="module">Модуль</label></td>
    <td valign="top"><input name="module" type="text" id="module" value="~PMODULE~" maxlength="20" /></td>
  </tr>
  <tr>
    <td valign="top"><label for="action">Действие</label></td>
     <td valign="top"><input name="action" type="text" id="action" value="~PACTION~" maxlength="100" /></td>
  </tr>
  <tr>
    <td valign="top"><label for="access">Уровень доступа</label></td>
    <td valign="top">  <input name="access" type="text" id="access" value="~PACCESS~" maxlength="20" /></td>
  </tr>
    <tr>
    <td valign="top"><label for="roles">Группы пользователей (кому разрешено)</label></td>
    <td valign="top"><textarea cols="60" rows="6" name="roles" id="roles">~PROLES~</textarea></td>
  </tr>
</table>
<input type="hidden" value="~PMODULE~" name="old_module" id="old_module" />
<input type="hidden" value="~PACTION~" name="old_action" id="old_action" />
<input type="hidden" value="~PACCESS~" name="old_access" id="old_access" />
<input class="btn" type="submit" name="button" id="button" value="Применить">
</form>


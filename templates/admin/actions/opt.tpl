<form action="/admin/reestr/" method="post">
<input type="hidden"  name="mod_action" value="~ACTION~" />
  <table width="100%" border="0">
  <tr>
    <td><label for="optname">Название настройки</label></td>
    <td><input size='80' name="optname" type="text" id="optname" value="~optname~" maxlength="255" /></td>
  </tr>
  <tr>
    <td><label for="parent">Описание настройки</label></td>
    <td><textarea cols="80" rows="6" name="optnote" type="text" id="optnote">~optnote~</textarea>
    </td>
  </tr>
  <tr>
    <td><label for="optvalue">Значение настройки</label></td>
    <td><textarea cols="80" rows="6" name="optvalue" type="text" id="optvalue">~optvalue~</textarea>
    </td>
  </tr>
</table>
<input type="hidden" value="~optname~" name="old_optname" id="old_optname" />
<input class="btn" type="submit" name="button" id="button" value="Применить">
</form>


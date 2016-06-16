<form action="/admin/users" method="post">
<input type="hidden"  name="mod_action" value="~ACTION~" />
  <table width="100%" border="0">
    <tr>
      <td align="left" valign="top">
      <input name="user" id="user" type="hidden" value="~USER~" />
      Имя пользователя: ~USER~  <br/>
    </td>
    </tr>
    <tr>
      <td valign="top" align="left">
       <label for="role">Права</label>
~ROLE~
    </tr>
  </table>
    <input class="btn" type="submit" name="button" id="button" value="Применить">
</form>
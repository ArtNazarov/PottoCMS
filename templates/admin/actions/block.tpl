<form action="/admin/blocks/" method="post">
<input type="hidden"  name="mod_action" value="~ACTION~" />
  <table width="100%" border="0">
  <tr>
    <td><label for="blockname">Переменная для шаблона</label></td>
    <td><input name="blockname" type="text" id="blockname" value="~BLOCKNAME~" maxlength="20" />
	</td>
  </tr>  
  <tr>
    <td><label for="blockdescription">Пояснение к блоку</label></td>
    <td><textarea name="blockdescription" type="text" id="blockdescription" >~BLOCKDESCRIPTION~</textarea></td>
  </tr>
  <tr>
    <td><label for="blockview">Текст блока</label></td>
    <td><textarea name="blockview" type="text" id="blockview" >~BLOCKVIEW~</textarea></td>
  </tr>
</table>
<input type="hidden" value="~OLD_BLOCKNAME~" name="old_blockname" id="old_blockname" />
<input class="btn" type="submit" name="button" id="button" value="Применить">
</form>


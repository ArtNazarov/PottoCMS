<form action="/admin/styles/" method="post">
<input type="hidden"  name="mod_action" value="~ACTION~" />
  <table width="100%" border="0">
  <tr>
    <td><label for="stylename">Идентификатор таблицы стилей</label></td>
    <td><input name="stylename" type="text" id="stylename" value="~styleNAME~" maxlength="20" />
	</td>
  </tr>  
  <tr>
    <td><label for="styledescription">Пояснение к таблице стилей</label></td>
    <td><textarea name="styledescription" type="text" id="styledescription" >~styleDESCRIPTION~</textarea></td>
  </tr>
  <tr>
    <td><label for="styleview">Инструкции CSS</label></td>
    <td><textarea name="styleview" type="text" id="styleview" >~styleVIEW~</textarea></td>
  </tr>
</table>
<input type="hidden" value="~OLD_styleNAME~" name="old_stylename" id="old_stylename" />
<input class="btn" type="submit" name="button" id="button" value="Применить">
</form>

<p class='note'>Подсказка: стили доступны по href='/style.php?stylename=идентификатор_стиля'
Чтобы подключить все стили, используйте переменную SYS_STYLES.
Если выбор стиля будет зависеть от переменной, используйте просто href='/style.php?stylename=~VAR~'
</p>


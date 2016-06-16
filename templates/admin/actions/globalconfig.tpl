<form action="/admin/configure" method="post">
<input type="hidden" name="mod_action" value="~ACTION~">
<label for="SITE_NAME">Имя сайта</label><br />
<input size='80' name="SITE_NAME" type="text" value="~SITE_NAME~" />
<br />
<label for="SITE_NAME">Кэширование</label><br />
<br/>~CACHE~<br />
<label for="META_DESCRIPTION">Описание сайта</label><br/>
<textarea cols="80" rows="3" id="META_DESCRIPTION" name="META_DESCRIPTION">~META_DESCRIPTION~</textarea>
<br />
<label for="META_KEYWORDS">Ключевые слова</label><br/>
<textarea cols="80" rows="3" id="META_KEYWORDS" name="META_KEYWORDS">~META_KEYWORDS~</textarea>
<br/><br/>
<label for="EMAIL_ADMIN">E-mail для обратной связи</label><br />
<input size='80' name="EMAIL_ADMIN" type="text" value="~EMAIL_ADMIN~" /><br />
<label for="CLOSED">Сайт закрыт?</label><br />
<br/>~CLOSED~ <br/>
<label for="CLOSED">Сообщение в случае закрытия</label><br />
<textarea name="CLOSED_MESSAGE" rows="3" cols="80">
~CLOSED_MESSAGE~
</textarea><br />
<label for="MAIN_TEMPLATE">Основной шаблон сайта</label><br />
<input size='80' name="MAIN_TEMPLATE" type="text" value="~MAIN_TEMPLATE~" /><br />
<input class="btn" type="submit" name="button" id="button" value="Применить">
</form>
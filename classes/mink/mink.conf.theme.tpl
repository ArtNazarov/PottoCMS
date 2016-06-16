<form action="/forum/newtheme" method="POST">
<input type="hidden" name="forum_url" value="~FORUM_URL~" /><br/>
Название темы:
<input type="text" name="theme_name" value="~THEME_NAME~"><br/>
Первое сообщение в теме:<br/>
<textarea name="message" cols="60" rows="5">
~MESSAGE~
</textarea><br/>
Пользователь:~USERNAME~<br/>
<input type="hidden" name="username" value="~USERNAME~">
<input type="submit" value="Создать тему и первое сообщение">
</form>

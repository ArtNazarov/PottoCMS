<form action="/pm/sendpm" method="post">
<input type="hidden" id="from" name="from" value="~USERNAME~"/>
<label for="message">Получатель</label><br />
<input type="text" id="to" name="to"  /><br />
<label for="message">Сообщение</label><br />
<textarea id="message" name="message" cols="40" rows="4">
</textarea><br/>
<input class="btn" type="submit" value="Отправить" />
</form>
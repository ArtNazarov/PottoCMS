<form method="post" action="/user/addcomment">
<input type="hidden" name="page" value="~ID~"/>
<input type="hidden" name="username" value="~USERNAME~"/>
<input type="hidden" name="created" value="~CREATED~"/>
<laber for="rating">Ваша оценка</label>
<select name="rating" id="rating">
<option value="5">+5</option>
<option value="4">+4</option>
<option value="3">+3</option>
<option value="2">+2</option>
<option value="1">+1</option>
<option value="0" selected>воздержусь, нейтрально</option>
</select><br/>
<textarea name="comment" id="comment" cols="50" rows="4">
</textarea>
<br/>
~CAPTCHA~
<input class="btn" type="submit" value="Добавить комментарий" />
</form>


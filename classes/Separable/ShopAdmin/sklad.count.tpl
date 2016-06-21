<div style="-webkit-border-radius: 6px;
		-moz-border-radius: 6px; padding:5px; margin:5px;
		border-radius: 6px; background-color:#ffffff;">
~ACTION_NAME~
<form  method="POST" action="/sklad/~FORM_ACTION~">
<input name="action" type="hidden" value="~FORM_ACTION~" />
Артикул:~ARTIKUL~<br/>
<input type="hidden" name="artikul" value="~ARTIKUL~"/><br/>
(Расшифровка)<br/>
~DESCRIPTION~
Количество:<br/>
<input type="text" name="count" value="~COUNT~"/><br/>
<input type="submit" value="~BUTTON_NAME~"/>
</form>
</div>
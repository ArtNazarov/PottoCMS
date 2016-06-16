<div style="-webkit-border-radius: 6px;
		-moz-border-radius: 6px; padding:15px; margin:5px;
		border-radius: 6px; 
border: 1px solid #9C9;
background-color: #EFE;
">
<form  method="POST" action="/shop/buyer/~FORM_ACTION~">
<input name="action" type="hidden" value="~FORM_ACTION~" />
<table border="0" cellpadding="15" cellspacing="15" width="100%">
<tr><td valign="top" align="center">
<img src='~PHOTO~' style='height:auto' height="auto" width="32"  /> 
</td>
<td>Вы добавили товар:<br/>
~DESCRIPTION~<br/>
Артикул:~ARTIKUL~ / Тип товара:~TYPE~<br/>
</td>
</tr></table>
<b>~ACTION_NAME~</b>
<input type="text" name="count" value="1"/><br/>
<input type="hidden" name="artikul" value="~ARTIKUL~"/>
<input type="hidden" name="description" value="~DESCRIPTION~"/>
<input type="hidden" name="description" value="~TYPE~"/>
<input type="submit" value="~BUTTON_NAME~"/>
</form>
</div>
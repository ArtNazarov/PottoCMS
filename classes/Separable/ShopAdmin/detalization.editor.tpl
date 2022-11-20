<div style="-webkit-border-radius: 6px;
		-moz-border-radius: 6px; padding:5px; margin:5px;
		border-radius: 6px; background-color:#ffffff;">
~ACTION_NAME~
<form  method="POST" action="/sklad/~FORM_ACTION~">
<table width="100%" border="0" cellpadding="5" cellspacing="5">
<tr>
<td valign="top" width="40%">
<input name="action" type="hidden" value="~FORM_ACTION~" />
Статус операции:<br/>
~STATUS~<br/>
Тип сделки:<br/>
~DTYPE~<br/>
Пользователь:<br/>
<input name="username" type="text" value="~USERNAME~" /><br/>
Дата:<br/>
<input name="date" type="text" value="~DATE~" /><br/>
Агент(ФИО, адрес, телефон):<br/>
<textarea name="agent" cols="45" rows="5">
~AGENT~
</textarea>
<script type="text/javascript" src="/vendor/ckeditor/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
CKEDITOR.replace( 'agent');
</script>
</td>
<td valign="top">
~BILL~
</td></tr></table>
<br/>
</table><br/><input type="submit" value="~BUTTON_NAME~"/><br/><hr/>
<a href="/sklad/orderdelete/~OPERATION~">УДАЛИТЬ ДАННУЮ ОПЕРАЦИЮ</a> 
</form>
</div>
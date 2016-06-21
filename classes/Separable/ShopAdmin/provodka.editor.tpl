<div style="-webkit-border-radius: 6px;
		-moz-border-radius: 6px; padding:5px; margin:5px;
		border-radius: 6px; background-color:#ffffff;">
~ACTION_NAME~
<form  method="POST" action="/sklad/~FORM_ACTION~">
<input name="action" type="hidden" value="~FORM_ACTION~" />
Тип сделки:
<select name="dtype" type="listbox">
<option value="plus">завоз товаров(плюс)</option>
<option value="minus" selected>продажа товаров(минус)</option>
</select><br/>
Агент(ФИО, адрес, телефон):<br/>
<textarea name="agent" cols="55" rows="5">
</textarea><br/>
<script type="text/javascript" src="/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
CKEDITOR.replace( 'agent');
</script>
~BILL~
</table><br/><input type="submit" value="~BUTTON_NAME~"/><br/><hr/>
<a href="/sklad/clearbill">Очистить накладную</a> <a href="/sklad/view">Выбрать еще товар для накладной...</a>
</form>
</div>
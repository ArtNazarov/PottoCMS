~ACTION_NAME~
<form  method="POST" action="/sklad/~FORM_ACTION~">
<input name="action" type="hidden" value="~FORM_ACTION~" />
Тип сделки:
<select name="dtype" type="listbox">
<option value="plus">покупка</option>
<option value="minus" selected>продажа</option>
</select><br/>
Дата:<br/>
<input name="date" type="datetime" /><br/>
Агент(ФИО, адрес, телефон):<br/>
<textarea name="agent" cols="55" rows="5">
~AGENT~
</textarea><br/>
<script type="text/javascript" src="/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
CKEDITOR.replace( 'agent');
</script>
~BILL~
</table><br/><input type="submit" value="~BUTTON_NAME~"/><br/><hr/>
<a href="/sklad/clearbill">Очистить накладную</a> <a href="/sklad/view">Выбрать еще товар для накладной...</a>
</form>
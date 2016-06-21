<script language='javascript' type='text/javascript'>
    function confdel()
    {
        if (confirm('Подтверждаете удаление?'))
            {
                    document.ftypeeditor.action='/sklad/deltype';
                    document.ftypeeditor.submit();
            };
    }
</script>
<div style="-webkit-border-radius: 6px;
		-moz-border-radius: 6px; padding:5px; margin:5px;
		border-radius: 6px; background-color:#ffffff;">
<form method="POST" name='ftypeeditor' id='ftypeeditor' action="/sklad/~FORM_ACTION~" >
<input type="button" style='~DELSTYLE~' name="delme" value="Удалить группу товаров полностью" onclick="confdel();"/><br/>
<input type="hidden" name="old_category" value="~CATEGORY~"/>
Группа товаров:<br/>
<input type="text" name="category" value="~CATEGORY~"/><br/>
Родительская группа:<br/>
~PARENT~<br/>
Описание:<br/>
<input type="text" name="catname" value="~CATNAME~"/><br/>
<input type="submit" value="~BUTTON_NAME~"/>
</form>
</div>
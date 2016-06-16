<script type="text/javascript" language="javascript">
    function gotopage(url)
    {
        document.location.href = url;
    }
    function submittopage(url)
    {
    document.fpr.action = url;
    document.fpr.submit();
    }
    </script>
<div style="-webkit-border-radius: 6px;
		-moz-border-radius: 6px; padding:5px; margin:5px;
		border-radius: 6px; background-color:#ffffff;">
~ACTION_NAME~
<form  method="POST" name='fpr' action="/shop/buyer/~FORM_ACTION~">
<input name="action" type="hidden" value="~FORM_ACTION~" />
Дата:<br/>
<input name="text" type="text" value="~DATE~" /><br/>
Покупатель(ФИО, адрес, телефон):<br/>
<textarea name="agent" cols="80" rows="5" style="width:100%">
</textarea><br/>
~CART~
</table><br/><br/>
<input type="button"  class="btn" value="Пересчитать" onclick="submittopage('/shop/buyer/recalc');" /> <input type="button" class="btn" value="Очистить корзину" onclick="gotopage('/shop/buyer/clearcart');" /> <input type="button" class="btn" value="Продолжить покупки" onclick="gotopage('/shop/view');" /> <input class="btn btn-primary" type="submit" value="~BUTTON_NAME~"/>
</form>
</div>
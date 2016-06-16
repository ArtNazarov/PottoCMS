<script language="javascript">
     function goafterconfirm(message, url)
     {
      if (confirm(message)) 
            {
       document.location.href = url
            };   
     }
</script>
<style>
.item:hover {background-color:#9FC !important}
#uihead {background-color:#C60; color:#FFF};
#action {background-color:#900; color:#FFF}
</style>

</style>
<div style="-webkit-border-radius: 6px;
		-moz-border-radius: 6px; padding:5px; margin:5px;
		border-radius: 6px; background-color:#ffffff;">
<h4>СПИСОК ТОВАРОВ НА СКЛАДЕ</h4>
<a href="/sklad/fnew"><img src='/images/add.gif' />Добавить новый артикул</a> | <a href="/sklad/fprovodka"><img src='/images/calc.gif' />Форма накладной(~BILL_COUNT~) </a> | <a href="/sklad/vieworders/all/1"><img src='/images/report.jpg' height='16' /> Операции и заказы...</a> 

<br/><hr/>
Вы в разделе ~BREADCRUMBS~
<table width="100%">
<tr>
<td valign="top" width="100">
	Категории:<br/>
	~CATEGORIES~<br/>
	<img src='/images/add.gif' /><a href="/sklad/fnewtype">Добавить новую категорию</a>
        <hr/>
        <img src='/images/add.gif' /><a href="/sklad/faddcall">Зарегистрировать звонок!</a><br/>
        <a href="/sklad/viewcalls">Журнал принятых звонков</a>
</td>
<td valign="top">
<table width="100%" class="sklad" border="1" >
		<tr id="uihead">
		<td valign="top">Фото товара</td>
		<td valign="top">Артикул</td>
		<td valign="top">Тип товара</td>
		<td valign="top">Описание изделия</td>
		<td valign="top">Цена</td>
		<td valign="top">Количество</td>
		<td valign="top">Примечания</td>
		<td id="action" valign="top">действия</td>
		</tr>
		~ITEMS~
		</table><br/>
~PAGES~
</td>
</tr>
</table>
<br/>
<hr/>
<a href="/sklad/createsitemap">Обновить список товаров для поисковиков</a> - <a href="/sklad/fprintprice">Печать ценников...</a>
</div>
<h6><sup>
&copy; СКЛАДСКОЙ УЧЕТ ДЛЯ Potto CMS. Назаров Артем Александрович, <a href="http://artnazarov.ru">Artnazarov.ru</a>, 2011
</sup></h6>
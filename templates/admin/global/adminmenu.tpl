<form name="logs" action="/admin/logs" method="POST" style="display:hidden">
<input type="hidden" name="mod_action" value="" />
<input type="hidden" name="log" value=""/>
</form>
<script type="text/javascript">
function viewlog(l)
{
	document.forms.logs.mod_action.value="viewlog";
	document.forms.logs.log.value=l;
	document.forms.logs.submit();
}
function clearlog()
{
	document.forms.logs.mod_action.value="clearlog";
	document.forms.logs.submit();
}
</script>

<ul id="hmenu" style="width:960px; background-image:url('/images/menubg.jpg');background-repeat:y-repeat;">
<li><a href="#">Главное</a>
<ul>
 <li><a href="../../" target="_blank">Просмотр сайта</a>
 <li><a href="/admin/reestr">Реестр настроек</a>
 <li><a href="/admin/sitemap">Карта сайта</a>
 <li><a href="/admin/clearcachefolders">Очистить кэш</a></li>
</ul> 
 </li>
 <li><a href="#">Пользователи и права</a>
 <ul>
 <li><a href="/admin/usergroups">Группы</a></li>
 <li><a href="/admin/users">Пользователи</a></li>
 <li><a href="/admin/permissions">Права</a></li>
 </ul></li>
  <li><a href="/admin/filemanager/">Файлы</a></li>    
  <li><a href="#">Внешний вид сайта</a>
 <ul>      
  <li><a href="/admin/blocks">Глобальные блоки</a>
  <li><a href="/admin/styles">Стили оформления</a>      
      </ul></li>
<li><a href="#">Наполнение</a>      
    <ul>
 <li><a href="/admin/categories">Категории</a>
 <li><a href="/admin/pages">Страницы</a>
 <li><a href="/admin/comments">Комментарии</a></li>
 </ul></li>
<li><a href="#">Журнал событий (логи)</a>
 <ul> 
 <li><a href="#" onclick="javascript:viewlog('sql');">Запросы к базе данных</a></li>
 <li><a href="#" onclick="javascript:viewlog('ips');">Даты и IP посещений</a></li>
 <li><a href="#" onclick="javascript:viewlog('jerboa');">Компонент Jerboa</a></li>
 <li><a href="#" onclick="javascript:viewlog('timing');">Производительность</a></li>
 <li><a href="#" onclick="javascript:viewlog('exceptions');">Сбои и ошибки</a></li>
 <li><a href="#" onclick="javascript:viewlog('auth');">Действия пользователей</a></li>
 <li><a href="#" onclick="javascript:clearlog();">Очистить журналы</a></li>
 </ul></li>
  <li><a href="/admin/logout">Выход</a></li>
</ul>
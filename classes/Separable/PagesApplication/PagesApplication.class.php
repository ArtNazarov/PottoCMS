<?php // Классы
if (!defined('APP')) {die('ERROR pages.class.php');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';


class PagesApplication
{
	var $components; // Компоненты
	var $cache_filename;
	var $cache_exists;
	var $cache_actually;
	var $kernel; // Ядро
        var $system; // Системные данные
	
	function __construct(&$params)
	{	
	$this->components = null;
  	$this->components['factory'] = new ClassFactory($params); // Фабрика классов
	 // Настройки базы данных
     if (($params['db']!=null) && (is_object($params['db'])))
     { // Не создаем новый объект базы данных, используем переданный
     $this->components['db'] = &$params['db'];
     }
     else
     { // Конструируем новый объект базы данных
 	 $this->components['db'] = $this->components['factory']->createInstance("DatabaseLayer", $params, 'Core');
 	 };
 	 $this->components['db']->Plug();
     $gears = @array(
	       
			'view' => 
                            array(
                             'classname'=> 'TemplateTool',
                             'category' => 'core'),
       
			'options' => 
                            array (
                                'classname'=>'CommonSettings',
                                'category'=>'Controllers'
                                ),
               
			'comments' => 
         
                            array (
                                'classname' =>  'Comments',                          
                                'category' => 'Embeddable'
                                ),
                        'formitems' =>          
                            array (
                                'classname' => 'FormItems',
                                'category' => 'Core'                                
                                ),
			'usr' =>          
                            array (
                                'classname' => 'UserAuth',
                                'category' => 'Services'
                                ),
			'categoryitem' =>          
                            array (
                               'classname' => 'SiteCategoryItem',
                               'category' => 'Models',
                                ),
			'feedback' =>          
                            array (
                                'classname' => 'Emailing',
                                'category' => 'Embeddable'
                                ),
			'rss' => 
                            array (
                                'classname' =>  'RssTool',
                                'category' => 'Separable'
                                ),
			'pm' =>         
                            array (
                                'classname' => 'PrivateMessages',
                                'category' => 'Embeddable'
                                ),
			'chat' => 

                            array (
                                'classname' => 'Chat',
                                'category' => 'Embeddable'
                                ),
			'sklad' =>
         
                            array (
                        'classname' => 'ShopAdmin',
                        'category' => 'Separable'),
         
			'shop' => 
                        
                        array (
                        'classname' => 'Shop',
                        'category' => 'Separable'),
         
			'forum'	=> 
         
                            array (
                              'classname'  => 'Forum',
                              'category' => 'Separable'),
                        'log' =>                             
                            array (
                                'classname'      => 'Log',
                                'category' => 'Core'
                                ),
                        'captcha' => 
                           array (
                               'classname'=> 'CaptchaTool',
                               'category' => 'Services'),                               
                        'adminfuncs'	=>
                            array(
                               'classname' => 'AdminFuncsPages',
                                'category' => 'Controllers'
                                )
			);
	 $this->components['factory']->createInstances($this->components,  $gears, $params);    
	 // НАСТРОЙКА ШАБЛОНИЗАТОРА (БАЗОВЫЙ ШАБЛОН)
	 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->components['options']->getOption('MAIN_TEMPLATE'));
   	 $this->components['view']->SetGlobal('menu', "templates/readers/global/menu.tpl");
   	 $this->components['view']->SetGlobal('sidebar', "templates/readers/global/sidebar.tpl");	 
         $this->components['view']->SetVar('TRANSLATIONS', '');
         
         $this->system['username'] = $this->components['usr']->GetUsernameFromSession();
         $this->system['usergroup'] = $this->components['usr']->GetRole($this->system['username']);
         
         
	 }
         
        function MyLog($log, $line, $message) 
        {
            $username = $this->system['username'];
            $usergroup =$this->system['usergroup'];
            $this->components['log']->WriteLog($log, "$message  by $username of $usergroup at $line\n");            
        }
	
        function __destruct()
        {
		if (isset($this->components))
		{
       foreach ($this->components as $key => $value)
	  {
		if (isset($this->components[$key])) {  unset($this->components[$key]);};
	  }
	  unset($this->components);
	  };
        }

   function PlugStyles()        
   {
$this->components['db']->setTable('styles');
$this->components['db']->Select(' * ', " 1 = 1");
$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
    {
        $this->components['view']->AddStyle($data['stylename']);
    };
    $this->components['log']->WriteLog('Pages', 'plugstyles done');
   }
        
        
    function site_closed_msg()
	{
      $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
	  $this->components['view']->SetVars(
                  array(
                        'SYS_TITLE' => 'Сообщение',
                        'SYS_MESSAGE' => $this->components['options']->getOption("CLOSED_MESSAGE"),
                        'LINK_HREF' => "/$aID",
                        'LINK_TITLE' => 'обновите страницу или зайдите позже'
          ));
	  $this->components['view']->CreateView();
	  $this->components['view']->Publish();
	}
	
	function clear_globals()
	{
            $this->components['view']->SetVars(
                array(
                'LANGUAGE_SWITCHER'=>'',
                'TITLE'=>'',
                'BREADCRUMBS'=>'',
                'COM_BODY'=>'',
                'COM_FORM'=>'',
                'ATTRS'=>'TITLE',
                'BODY'                   
                        )
                  );
	}

	function total_queries()
	{
	  $q  = $this->components['db']->query_counter;
          $arr = array('db', 'options', 'comments', 'usr', 'feedback', 'rss', 'pm');
          foreach ($arr as $name)
          {             
            @ $q = $q + $this->components[$name]->components['db']->query_counter;
          }
		return $q;
	}

	function user_area()
	{
		if (($this->components['usr']->UserChecking())==true) {
$this->components['view']->SetVar('USER_AREA', $this->components['usr']->UserProfile());

 }
 else
 { $this->components['view']->SetVar('USER_AREA', $this->components['usr']->UserLogForm()); //  Профиль пользователя
 };
	}

	function latest_articles($aCategory, $article_num)
	{
            $lang = $this->DetectLanguage();
            	$this->components['db']->setTable('translations');
		$this->components['db']->Select(' * ', " lang = '$lang' ");
                $repl = array();
                
                $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
                {
                    $repl[$data['id']] = $data['caption'];
                
                };            
		$this->components['db']->setTable('pages');
		$this->components['db']->Select(
		"id, title, created",
		"category = '$aCategory' ORDER BY created DESC LIMIT 0 , $article_num");
		$block = "";
		$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
		{
			  $date = $data['created'];
			  $title = $data['title'];                          
			  $id = $data['id'];
                          if ( isset($repl[$id]) )
                          {
                          $title = $repl[$id];
                       
                          };
			  $block .= "<p><span class='subheading'>$date</span><br/><a href='/content/$id'>$title</a></p>";
		}
        $block .= "&nbsp&nbsp;&rarr;&nbsp&nbsp<a href='/content/category/$aCategory'>все новости...</a>";
		return $block;
	}

    function breadcrumbs($aCategory)
	{
	// Хлебные крошки
	$h = '';
	$Category = $aCategory;
	$Parent = '';
    $Cat_name = '';
	$num_iter = 3;
	for ($i=1; $i <= $num_iter; $i++) {

	$this->components['db']->setTable('categories');
    $this->components['db']->Select('parent, cat_name', "category='$Category'");
	$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
	{
    $Cat_name = $data['cat_name'];
	$Parent =  $data['parent'];
	};

    $h = " →  <a href=/content/category/$Category>$Cat_name</a> ".$h;
	if ($Parent == '') break;
    if ($Parent == 'root') break;
	$Category = $Parent;
	$num_iter--;
	 };
	 if ($h!='') return $h;
	}
	function pageid()
	{
	    if (empty($_GET['id']))
{  return 'mainpage';}
else
{return $_GET['id']; };
	}
	function PlugGlobals()
        {
         // Подключаем вызов таблиц стилей
         $this->PlugStyles();
	 // Подключаем глобальные блоки, указанные в таблице blocks
	 $this->components['db']->SetTable('blocks');
	 $this->components['db']->Select(' * ', ' 1 = 1 ');
	 $rows = $this->components['db']->Read();
         foreach ($rows as $i => $record)
	 {
	 $this->components['view']->SetVar($record['blockname'], $record['blockview']);
	 };
     $this->components['view']->SetGlobal('menu', "templates/readers/global/menu.tpl");
  	 $this->components['view']->SetGlobal('sidebar', "templates/readers/global/sidebar.tpl");
 $this->components['db']->setTable('pages');
 
 $this->components['view']->SetVars(            
 array(
 'SPEC_LINK' => "",
 'META_DESCRIPTION'=> $this->components['options']->getOption('META_DESCRIPTION'),
 'META_KEYWORDS' => $this->components['options']->getOption('META_KEYWORDS'),
 'SITE_NAME' => $this->components['options']->getOption('SITE_NAME'),
 'LATEST_ARTICLES' => $this->latest_articles($this->components['options']->getOption('GET_ARTICLES_FROM'), 4),
 'W_ONLINE' => $this->components['usr']->GetUsersOnline()
         ));
 $this->user_area(); 
	}
	
	
	function AddCommentsAndRatings()
	{
	$this->components['view']->SetVar('COM_BODY', 
	  $this->components['comments']->GetComments(
				getenv('REQUEST_URI')) );	// Комментарии
     $this->components['view']->SetVar('COM_FORM',
	 		$this->components['view']->Choice($this->components['usr']->UserChecking(),
			$this->components['comments']->com_form($this->components['usr']->GetUsernameFromSession(),  getenv('REQUEST_URI')),
            'Оставить комментарии могут только авторизованные пользователи'
			)
	);
	}
	
        
        function FLangSwitcher($lang)
        {
            $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/misc/langselector.tpl');
            
            $arr = array('rus'=>'Русский', 'english'=>'English');
            $langs = $this->components['formitems']->SelectItem($arr, $lang, 'lang');            
            $this->components['view']->SetVar('LANGS', $langs);
            $this->components['view']->CreateView();
            return $this->components['view']->GetView();                    
        }
        
        function DetectLanguage()
        {
            $def = "rus";            
            isset($_SESSION['lang']) ? $sess_lang = $_SESSION['lang'] : $sess_lang = $def;            
            $lang = $sess_lang;
            return $lang;            
        }
        
          function Vavilon()
        {
            $lang = $this->DetectLanguage();
            $this->components['db']->setTable('voc');            
            $this->components['db']->Select(' * ', " lang = '$lang' ");
            $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
            {
              $this->components['view']->ReplView($data['orig'], $data['wrd']);
            };            
        }
        
      
	
        
	function viewpage()
	{
            
            
	// Синоним параметра
    //$this->kernel->set_alias(0, 'id');
            
            $page = array(
                                'TITLE'=>'',
                                'BODY'=>'',
                                'BREADCRUMBS'=>'',
                                'COM_BODY'=>'',
                                'COM_FORM'=>'',
                                'ATTRS'=>'');
                                   
          $lang = $this->DetectLanguage();
	  $languageSwitcher = $this->FLangSwitcher($lang);
  	   $this->PlugGlobals();

     $aId = $this->pageid();
     $this->components['db']->setTable('pages');
	 
     $this->components['db']->Select(' * ', "id='$aId'");

     $d =  $this->components['db']->Read();
	 if ($this->components['db']->NRows()==0)
	 {
       $page['TITLE'] = 'Страница не найдена';
       $this->components['view']->SetVars($page);
	 };
	 if   (($this->components['db']->NRows()>0) and ($d['status']=="bin"))
		 { 
       $page['TITLE'] = 'Материал был временно удален!';
       $this->components['view']->SetVars($page);
		 }
	 if   (($this->components['db']->NRows()>0) and ($d['status']=="draft"))
		 {     
       $page['TITLE'] = 'Материал в черновике';
       $this->components['view']->SetVars($page);
		 }
	 if   (($this->components['db']->NRows()>0) and ($d['status']=="public"))
		 {
	    $page['TITLE'] = $d['title'];
            $page['BODY'] = $d['body'];
            $page['BREADCRUMBS'] = $this->breadcrumbs($d['category']);
            $visitors_new = $d['visitors']+1;
            $page['ATTRS'] = "Автор: ".$d["username"]." Дата: ".$d["created"] . " Просмотров: ". $visitors_new;
	 
	if ($this->system['usergroup'] == 'admin')
	{
            $page['SPEC_LINK'] = "[<a href=/special/edit/$aId><img src='/images/edit.gif'/>Правка</a>]";
            };
	 $this->components['db']->setTable('pages');
	 $this->components['db']->Update("visitors=$visitors_new", "id='$aId'"); // Статистика :)))
	 };

    $this->AddCommentsAndRatings();            
    $page['TRANSLATIONS'] = $this->GetTranslationsLinks($d['id']);    
	 // Выбираем шаблон оформления, соответствующий категории данной страницы
	 $this->components['categoryitem']->getCategory($this->components['db'], $d['category']);
	 $template = $_SERVER['DOCUMENT_ROOT'].$this->components['categoryitem']->cat_template;
	 if ($template==$_SERVER['DOCUMENT_ROOT']) {$template=$_SERVER['DOCUMENT_ROOT'].'/templates/readers/bootstrap.tpl';};
	 $page['DEBUG_INFO'] = 'Запросов:'.$this->total_queries()." Шаблон ".$template;
         $page['LANGUAGE_SWITCHER'] = $languageSwitcher;                                    
         $this->components['view']->UseTpl($template);            
         $this->components['view']->SetVars($page);                  
         $this->LoadTranslation($d['id'], $lang);                 
	 $this->components['view']->CreateView();        
         $this->Vavilon();
         $this->components['db']->Done();
	 $this->components['view']->Publish();
         $this->components['log']->MyLog('Pages', 'viewpage end');
	}

        function GetTranslationsLinks($id)
        {
            $clang = $this->DetectLanguage(); // Текущий язык            
             $this->components['db']->Plug();
             $this->components['db']->setTable('translations');
             $this->components['db']->Select(" * ", "id='$id'");
             $lang = 'rus';
             $links = "<br/><i>Доступные языки: &nbsp;<span onclick='reloadlng(\"$lang\");'>$lang</span>&nbsp;";
             $t = 0;
                 $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
                 {
                     $t = $t + 1;                     
                     $lang = $data['lang'];
                     if ($lang != $clang)
                     {
                     $links .= "&nbsp;<span onclick='reloadlng(\"$lang\");'>$lang</span>&nbsp;";
                     }
                     else
                     {
                     $links .= "&nbsp;<span style='color:#fff;background-color:#000'>$lang</span>&nbsp;";
                     };
                 };       
             $links .= "</i>"    ;
                 return $links;             
        }

        function LoadTranslation($id, $lang)
        {
        if ($lang != 'rus')
            {
             $this->components['db']->setTable('translations');
             $this->components['db']->Select("*", "( ( id='$id' ) AND (lang='$lang') )");
             if ($this->components['db']->NRows()>0)
             {
               $data = $this->components['db']->Read();
               $this->components['view']->SetVars(
                       array(
                          'TITLE' => $data['caption'],
                          'BODY' => $data['body']
                       ));
             };
            };
               
               
        }
    
	function listbycat()
	{
$lang = $this->DetectLanguage();
$languageSwitcher = $this->FLangSwitcher($lang);
// Алиасы

$this->components['db']->setTable('translations');
$this->components['db']->Select("*", " (lang='$lang') ");
$repl = array();
while ($transl = $this->components['db']->Read())
{
  $repl[$transl['id']] = $transl['caption'];
  $bdy[$transl['id']] = $transl['body'];
};


 $cols = $this->components['options']->getOption('COLUMNS'); // Число колонок
 $w = round((1 / $cols) * 100);
 $aCategory = $_GET['category']; // Категория из адресной строки
 $aPage = $_GET['page']; // Номер страницы
 $articles = $this->components['options']->getOption('ARTICLES'); // Число статей на странице

 $StartArticle = ($aPage-1)*$articles;
 $this->PlugGlobals();
 $c = 1; 
 $this->components['db']->setTable('pages'); // Сначала работаем со списком страниц
  $t = '<table class="list" width="100%">';    // Выдача списка страниц
  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/items/item.tpl');
  $this->components['db']->Select('category, id, title, body',
 "category='$aCategory'"." ORDER BY created DESC  LIMIT $StartArticle, $articles");
				$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
				{
				if ($c == 1) {$t .= "<tr>";};
				$t .= "<td class='listitem' valign='top' align='left' width='$w%'>";
					$this->components['view']->SetVar('ID', $data['id']);
                                    $title = $data['title'];
                                    if (isset($repl[$data['id']]))
                                    {
                                        $title = $repl[$data['id']];
                                    }
 				    $this->components['view']->SetVar('TITLE', $title);
                                    $bd = $data['body'];
                                    if (isset($bdy[$data['id']]))
                                    {
                                        $bd = $bdy[$data['id']];
                                    };
                                    $bd = substr(strip_tags($bd, 0, 255));
 				    $this->components['view']->SetVar('SHORT_MESSAGE', $bd);
					$this->components['view']->CreateView();
					$t.= $this->components['view']->getView();
					$t .= '</td>';
				$c = $c + 1;
if ($c > $cols) {$t .= "</tr>"; $c = 1;};
				};
	$t .= "</table>";
   // Сформировали табличку со списком страниц в данной категории
   // Теперь список подкатегорий
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/items/item.tpl');
   $this->components['db']->setTable('categories');
  $this->components['db']->Select('category, cat_name, parent, template', "parent='$aCategory'");

  if ($this->components['db']->NRows()>0)
  {   $t .= "<br/><h4>Подкатегории</h4>";
				$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
				{
					$catname = $data['cat_name'];
					$catitem = $data['category'];
					$t.= "<a href='/content/category/$catitem'>$catname</a><br/>";
				};
  } else $t .= "<br/><i>Подкатегорий нет</i>";


     $this->components['categoryitem']->getCategory($this->components['db'], $aCategory);

     $template = $_SERVER['DOCUMENT_ROOT'].$this->components['categoryitem']->cat_template;
	 if ($template==$_SERVER['DOCUMENT_ROOT'])
	 {$template=$_SERVER['DOCUMENT_ROOT'].'/templates/readers/simple.tpl';};
	 $this->components['view']->SetVar('DEBUG_INFO', 'Запросов:'.$this->total_queries()." Шаблон ".$template);
     $this->components['view']->UseTpl($template);

 	 $this->components['view']->SetVar('BODY', $t);

	 $this->components['db']->setTable('categories');
	 $t = $this->components['db']->getCell('cat_name', "category='$aCategory'");
	 if ($t == '')
	 {
	 $this->components['view']->Error_msg("Категория не найдена или не существует");
	 };
 	 $this->components['view']->SetVar('TITLE', $t);
 	 $this->components['view']->SetVar('BREADCRUMBS', $this->breadcrumbs($aCategory));
	 $this->components['view']->SetVar('COM_BODY', '');	// Комментарии
     $this->components['view']->SetVar('COM_FORM', '');

	 // ПАГИНАЦИЯ

     $this->components['db']->setTable('pages');
	 $this->components['db']->Select('id', "category='$aCategory'");
     $TOTAL_PAGES = $this->components['db']->NRows();
	 $ost = 0;
	 if (($TOTAL_PAGES % $articles) > 0) {$ost = 1;};
	 $PAGES_SELECTOR_COUNT = floor($TOTAL_PAGES / $articles) + $ost;
	 if ( $PAGES_SELECTOR_COUNT >=1 )
	 {
	 $paginator = '';
	 for ($p=1; $p<=$PAGES_SELECTOR_COUNT; $p++)
	 {
		 $link = "<a href='/content/category/$aCategory/$p'>$p</a>&nbsp;";
		 $paginator .= $link;
	 };
	 } else $PAGES_SELECTOR_COUNT = 1;
     $this->components['view']->SetVar('ATTRS', "Страница $aPage из $PAGES_SELECTOR_COUNT<br/>".$paginator);
     $this->components['view']->SetVar('DEBUG_INFO', 'Запросов:'.$this->total_queries());
               $this->components['view']->SetVar('LANGUAGE_SWITCHER', $languageSwitcher);
   	 $this->components['db']->Done();
	 $this->components['view']->CreateView();
         $this->Vavilon();
	 $this->components['view']->Publish();
	}


	function chatpage()
	{
        $human= $this->system['username'];
	$role = $this->system['usergroup'];
	if ($role == "seller")	
	{
	
    if ($_POST['refresh']=="OK")
		   {$this->components['chat']->addmessage(date("m.d.y H:i:s"), $this->components['usr']->getUsernameFromSession(), $_POST['message']);};
		$this->PlugGlobals();

  	$this->components['view']->SetVar('BODY',
	$this->components['view']->Choice(  $this->components['usr']->UserChecking(),
		$this->components['chat']->getchatpage(), 'Чат доступен только для зарегистрированных пользователей'));
 	$this->components['view']->SetVar('TITLE', "Чат");
 	$this->components['view']->SetVar('BREADCRUMBS', '');
	$this->components['view']->SetVar('COM_BODY', '');	// Комментарии
    $this->components['view']->SetVar('COM_FORM', '');
    $this->components['view']->SetVar('ATTRS', '');
    $this->components['view']->SetVar('DEBUG_INFO', 'Запросов:'.$this->total_queries());
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	}
	else
	
	{$this->components['view']->tpl_view = "Доступ запрещен!";
	$this->components['view']->Publish();
	};
	}

	function writemail()
	{
	$this->PlugGlobals();
  	$this->components['view']->SetVars(
        array(
            'BODY' => $this->components['feedback']->mail_form(),                    
            'TITLE' => "Письмо владельцу",
            'BREADCRUMBS' => '',
            'COM_BODY' => '',	// Комментарии
            'COM_FORM' => '',
            'ATTRS' => '',
            'DEBUG_INFO' => 'Запросов:'.$this->total_queries()
                )
                );
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	}

	function sendmail()
	{
		$this->components['feedback']->mail_process();
	}

	function writepm()
	{
	$this->PlugGlobals();
  	$this->components['view']->SetVars(
        array(
            'BODY' => $this->components['pm']->form_write_pm(),
            'TITLE' => "Личное сообщение",
            'BREADCRUMBS' => '',
            'COM_BODY' => '',	
            'COM_FORM' => '',
            'ATTRS' => '',
            'DEBUG_INFO' => 'Запросов:'.$this->total_queries()
                )
                    );
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	}
	// Склад
	function skladpage()
	{
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/simple.tpl');	
	$human= $this->system['username'];	
	$role = $this->system['usergroup'];
	if ($role == "seller")	
	{
   $this->PlugGlobals();
  	$this->components['view']->SetVars(
                array(                    
                'BODY' => $this->components['sklad']->run(),
                'TITLE' => "<a href='/sklad/view'>Складской учет.</a> Сотрудник: ".$this->components['usr']->GetUsernameFromSession(),
                'BREADCRUMBS' => '',
                'COM_BODY' => '',
                'COM_FORM' => '',
                'ATTRS' => '',
                'DEBUG_INFO' => 'Запросов:'.$this->total_queries()
                ));
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	} else {$this->components['view']->tpl_view = "Доступ запрещен!";
	$this->components['view']->Publish();
	    }
	}


// Магазин
       
	function shoppage()
	{
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/bootstrap.tpl');
	
   $this->PlugGlobals();

  	$this->components['view']->SetVars(
                array(
        'BODY' => $this->components['shop']->run(),                    
	'META_DESCRIPTION' => $this->components['view']->GetVar('META_DESCRIPTION').' '.$this->components['shop']->meta,
	'META_KEYWORDS' => $this->components['view']->GetVar('META_KEYWORDS').' '.$this->components['shop']->meta,
 	'TITLE' => $this->components['view']->GetVar('TITLE').' '.$this->components['shop']->title,
 	'BREADCRUMBS' => '',
        'ATTRS' => ''
                    ));
	$this->AddCommentsAndRatings();	
        $this->components['view']->SetVar('DEBUG_INFO', 'Запросов:'.$this->total_queries());
        
	$this->components['view']->CreateView();
	$this->components['view']->Publish();	
	}



	function inboxpm()
	{
	$this->PlugGlobals();
  	$this->components['view']->SetVars(
                array(
                    'BODY' => $this->components['pm']->inbox_pm(),
                    'TITLE' => "Входящие:",
                    'BREADCRUMBS' => '',
                    'COM_BODY' => '',
                    'COM_FORM' => '',
                    'ATTRS' => '',
                    'DEBUG_INFO' => 'Запросов:'.$this->total_queries()
                ));
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	}


	function outboxpm()
	{
	$this->PlugGlobals();
  	$this->components['view']->SetVars(
              array(
                  'BODY' => $this->components['pm']->outbox_pm(),
    'TITLE' => "Исходящие:",
    'BREADCRUMBS' => '',
    'COM_BODY' => '',
    'COM_FORM' => '',
    'ATTRS' => '',
    'DEBUG_INFO' => 'Запросов:'.$this->total_queries()
            ));
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	}


	function openpm()
	{
	$aID = $_GET['id_pm'];
	$this->PlugGlobals();
  	$this->components['view']->SetVars(
                array(
                    'BODY' => $this->components['pm']->open_pm($aID),          
                    'TITLE' => "Читать сообщение",
                    'BREADCRUMBS' => '',
                    'COM_BODY' => '',
                    'COM_FORM' => '',
                    'ATTRS' => '',
                    'DEBUG_INFO' => 'Запросов:'.$this->total_queries()
                )
                );
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	}
	
	function comments()
	{
	if ($this->components['captcha']->check())
	{
	$this->components['comments']->AddComment($_POST['page']);
	$this->components['comments']->com_posted($_POST['page']);
	}
	else
	{
	$this->components['captcha']->msg_wrong_captcha($_POST['page']);
	};
	}
	
	function editpage()
	{
        $human= $this->system['username'];
	$role = $this->system['usergroup'];
	if ($role == "admin")	
	{
	
	$this->clear_globals();
	$this->PlugGlobals();
	
	$this->components['adminfuncs']->PagesEditPlugin($_GET['param']);
	$this->components['view']->SetVar('BODY', $this->components['adminfuncs']->ui);
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	};
	}
	
	function savepage()
	{
	$human= $this->system['username'];	
	$role = $this->system['usergroup'];
	if ($role == "admin")	
	{
	$this->clear_globals();
	$this->PlugGlobals();
	$this->components['adminfuncs']->PagesSavePage();
	$this->components['view']->SetVar('BODY', "<a href=/content/".$_GET['param'].">Страница</a> сохранена...");
	$this->components['view']->CreateView();
	$this->components['view']->Publish();
	};
	}
	

	function forum()
	{
	$this->components['forum']->Run();
	}

	function getTask()
	{
            isset($_GET['do']) ? $do = @$_GET['do'] : $do = 'viewpage';
	    return $do;                
	}

	 function secaction($aTask)
	{
			switch ($aTask)
		{
		    case 'logout' :  {  $this->components['usr']->UserLogOut();  exit; };
			// Заполнение регистрационной формы
			case 'regform' :
			{
			$this->PlugGlobals();
 $this->components['view']->SetVars(array(
         'TITLE' => 'Регистрация пользователя',
         'BODY' => $this->components['usr']->UserRegForm(),
         'BREADCRUMBS' => '',
        'COM_BODY' => '',
        'COM_FORM' => '',
        'ATTRS' => '',
        'DEBUG_INFO', 'Запросов:'.$this->total_queries()
         ));
 $this->components['view']->CreateView();
 $this->components['view']->Publish();
 exit;
			}
			// Регистрация
			case 'register' :
			{
$this->components['usr']->UserRegistration();
 exit;
			}
			// Заполнение формы для входа
			case 'logform' :
  		    {
 $this->PlugGlobals();
 $this->components['view']->SetVars(
         array(
            'TITLE' => 'Авторизация пользователя',             
            'BODY' => $this->components['usr']->UserLogForm(),
            'BREADCRUMBS' => '',
            'COM_BODY'  => '',	
            'COM_FORM' => '',
            'ATTRS'  => '',
            'DEBUG_INFO'  => 'Запросов:'.$this->total_queries()
         ));
 $this->components['view']->CreateView();
 $this->components['view']->Publish();
			exit;
			}
			// Авторизация
			case 'login' :
			{
	$this->components['usr']->UserLogin();
    break;
			}
		};
	}
	
	function showoptions()
	{
	$this->PlugGlobals();
	$vars = array(
		'COM_BODY' => '',
		'COM_FORM' => '',
		'TITLE' => 'Пользовательские настройки',
		'BODY'=> $this->components['usr']->w_UserOptions()
	 );	
    $this->components['view']->SetVars($vars);		 
    $this->components['view']->CreateView();		
	
    $this->components['view']->Publish();
	}
	
		function saveoptions()
	{
	$this->components['usr']->db_SaveUserOptions();
	$this->PlugGlobals();
	$vars = array(
		'COM_BODY' => '',
		'COM_FORM' => '',
		'TITLE' => 'Пользовательские настройки',
		'BODY' => '<a href="/options/showoptions/">Сохранены...</a><br/>'
		);
	$this->components['view']->SetVars($vars);	
	$this->components['view']->CreateView();
    $this->components['view']->Publish();
	}

	function NormalActions($aTask)
	{
		if (($this->components['options']->GetOption("CLOSED") == "ON" )
		and ($this->components['usr']->RoleCheck($this->components['usr']->GetUsernameFromSession(), 'admin')==false)
		)
 {		$this->site_closed_msg(); }
			else
		switch ($aTask)
		{
 	   	    case '' : { $this->viewpage(); break; };
			  // Просмотр списка категорий
			case 'listbycat' : { $this->listbycat(); break; };
			// Просмотр публикации
			case 'viewpage' : {$this->viewpage(); break; };
			// Добавление комментария
			case 'addcomment' : { $this->comments(); break; };
		  case 'writemail' : {$this->writemail(); break;};
  	      case 'sendmail' : {$this->sendmail(); break;};
		  case 'rss' : {$this->components['rss']->PublishRss($_GET['feed']); break;};
		  case 'writepm' : {$this->writepm(); break;};
		  case 'inboxpm' : {$this->inboxpm();break;};
		  case 'outboxpm' : {$this->outboxpm(); break;};
		  case 'openpm' : {$this->openpm(); break;};
		  case 'sendpm' : {$this->components['pm']->send_pm(); break;};
		  case 'chat'  : {$this->chatpage(); break;}
		  
		  case "edit" :  {$this->editpage(); break;}
    	  case "save" :  {$this->savepage(); break;}
		  
		  case "showoptions" : 
			{
					$this->showoptions(); break;
			};
		  case "saveoptions" :
			{
				$this->saveoptions(); break;
			};
		  		  
		  case "sklad" :  {$this->skladpage(); break;}
    	  case "shop" :  {$this->shoppage(); break;}
		  case "forum" :  {$this->forum(); break;}
		  
		};

	}

	function doAction($aTask)
	{
		$this->secaction($aTask);
		$this->NormalActions($aTask);
	}
	function Run()
	{
	  $this->doAction($this->getTask());
          $this->components['log']->WriteLog('Pages', 'run - OK');
	}


}
?>
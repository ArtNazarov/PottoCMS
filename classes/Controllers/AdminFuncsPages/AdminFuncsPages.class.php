<?php
error_reporting(0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';

class AdminFuncsPages
{
	var $components; // Компоненты
	var $ui; // Возвращает пользовательский интерфейс

	// НАЧАЛЬНЫЕ УСТАНОВКИ
	function __construct(&$params)
	{
		$this->components = null;
		$this->components['factory'] = new ClassFactory($params); // Фабрика классов
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
			 'pageitem' => 
                    array(
                        'classname' => 'SitePageItem',
                        'category' => 'Models'
                        ), // Страница
			 'categoryitem' => 
                        array(
                          'classname' => 'SiteCategoryItem', // Категория
                          'category' => 'Models'),
                    
			 'view' => array(
                            'classname' => 'TemplateTool', // Шаблонизатор
                            'category' => 'Core'),
                    
			 'options' => array(
                           'classname' =>  'CommonSettings', // Опции сайта
                           'category' => 'Controllers'),
			 'comments' => 
                            array( 
                                'classname' => 'Comments', // Комментарии
                                'category' => 'Embeddable'),
                                
			 'usr' =>  array(
                             'classname'=>'UserAuth', // Пользователи
                             'category' => 'Services'),
                    
                        'formitems' => array(
                            'classname' => 'FormItems', // Формы
                            'category' => 'Core'),
			 'blocks' => 
                    array('classname' => 'GlobalBlocksController', // Глобальные блоки
                            'category' => 'Controllers'),
                         'styles' => array(
                        'classname' => 'Styles',
                        'category' => 'Helpers' // Таблицы стилей!                        
			));
                
		$this->components['factory']->createInstances($this->components,
                $gears, $params);
        $this->components['db']->setTable('pages'); // Рабочая таблица в базе
	}
	function __destruct()
	{
	  foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
	}


	function ListConditions( $filter_status, $filter_category, $ordering  )
	{

                if ($filter_status == "") {$filter_status="_any";};
                if ($filter_category == "") {$filter_category="_any";};
                if ($ordering == "") {$ordering="_any";};

                if (($filter_category=="_any") &&($filter_status=="_any"))
                  {$filter = " 1 = 1 "; };

                if (($filter_category=="_any") && ($filter_status!="_any"))
                {
                $filter = " (status = '$filter_status' ) ";
                };
                if (($filter_category!="_any") && ($filter_status=="_any"))
                {
                	$filter = " (category = '$filter_category' )  ";
                };
                if (($filter_category!="_any") && ($filter_status!="_any"))
                {
                	$filter = " ( category = '$filter_category' ) AND ( status = '$filter_status' ) ";
                };

	            $sorting = "";
                if ($ordering != "_any") {$sorting=" ORDER BY '$ordering' "; };
                return "$filter $sorting";
	}
	
	

    function PagesClearBin()
    {
    	$this->components['pageitem']->clearBin($this->components['db']);
        $this->ui = 'Корзина очищена! <a href="/admin/pages">Вернуться к списку страниц</a> ';
    }

    function PagesEditPage()
    {
    	$this->ui = '<h3>Правка страницы</h3><br/>';
				$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/editor.tpl');
				$id = $_POST['id'];
				$this->components['pageitem']->getPage($this->components['db'], $id);
				$this->components['view']->SetVar('PAGE-ID', $id);
				$this->components['view']->SetVar('PAGE-TITLE', $this->components['pageitem']->title);
                $this->components['view']->SetVar('PAGE-BODY', $this->components['pageitem']->body);
                $arr = array ('public'=>'опубликовано', 'draft'=>'черновик');
				$this->components['view']->SetVar('STATUS',
					$this->components['formitems']->RadioItems(
						$arr,
						$this->components['pageitem']->status,
						'status')
					);
				$arr = $this->components['categoryitem']->getCategoriesArray(
								$this->components['db']
								);
					 $this->components['view']->SetVar('CAT',
              			$this->components['formitems']->SelectItem(
								$arr,
								$this->components['pageitem']->category,
								'category')
                );
				$this->components['view']->SetVar('USERNAME', $this->components['pageitem']->username);
				$this->components['view']->SetVar('CREATED', date("Y-m-d h:m:s"));
                $this->components['view']->SetVar('ACTION', 'savepage');
				$this->components['view']->CreateView();
				$this->ui .= $this->components['view']->getView();

      	}
		
	 function PagesEditPlugin($id)
    {
    	$this->ui = '<h3>Правка страницы</h3><br/>';
				$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/editor-plugin.tpl');
				
				$this->components['pageitem']->getPage($this->components['db'], $id);
				$this->components['view']->SetVar('PAGE-ID', $id);
				$this->components['view']->SetVar('PAGE-TITLE', $this->components['pageitem']->title);
                $this->components['view']->SetVar('PAGE-BODY', $this->components['pageitem']->body);
                $arr = array ('public'=>'опубликовано', 'draft'=>'черновик');
				$this->components['view']->SetVar('STATUS',
					$this->components['formitems']->SelectItem(
						$arr,
						$this->components['pageitem']->status,
						'status')
					);
				$arr = $this->components['categoryitem']->getCategoriesArray(
								$this->components['db']
								);
					 $this->components['view']->SetVar('CAT',
              			$this->components['formitems']->SelectItem(
								$arr,
								$this->components['pageitem']->category,
								'category')
                );
				$this->components['view']->SetVar('USERNAME', $this->components['pageitem']->username);
				$this->components['view']->SetVar('CREATED', date("Y-m-d h:m:s"));
                $this->components['view']->SetVar('ACTION', 'savepage');
				$this->components['view']->CreateView();
				$this->ui .= $this->components['view']->getView();

      	}	

    function PagesSavePage()
    {
    	$aNewID = $_POST['id'];
				$aOldID = $_POST['id_old'];
				$title = $_POST['title'];
				$body = $_POST['body'];
				$category = $_POST['category'];
				$status = $_POST['status'];
				$created = $_POST['created'];
				$username = $_POST['username'];
				$this->components['pageitem']->editPage($this->components['db'], $aOldID, $aNewID, $title, $body, $category, $status, $username, $created);
   	}

   	function PagesFillPage()
   	{
   	// Внесение материала
                $this->ui = '<h3>Внесение новой страницы</h3><br/>';
				$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/editor.tpl');
				$this->components['view']->SetVar('PAGE-ID', '');
				$this->components['view']->SetVar('PAGE-TITLE', '');
                $this->components['view']->SetVar('PAGE-BODY', '');
                $this->components['view']->SetVar('CAT',
              			$this->components['formitems']->SelectItem(
								$this->components['categoryitem']->getCategoriesArray(
								$this->components['db']
								),
								$_POST['filter_category'],
								'category')
                );
                $arr = array ('public'=>'опубликовано', 'draft'=>'черновик');
                	$this->components['view']->SetVar('STATUS',
					$this->components['formitems']->RadioItems(
						$arr,
						'public',
						'status')
					);
				$this->components['view']->SetVar('CREATED', date("Y-m-d h:m:s"));
				$this->components['view']->SetVar('USERNAME', $this->components['usr']->GetUsernameFromSession());
                $this->components['view']->SetVar('ACTION', 'addpage');
				$this->components['view']->SetVar('SYSTEM_MESSAGE', $_POST['mod_action']);
				$this->components['view']->CreateView();
				$this->ui .= $this->components['view']->getView();
   		}

   function PagesAddPage()
   {
   // Добавление материала
					$id = $_POST['id'];
					$title = $_POST['title'];
					$body = $_POST['body'];
					$category = $_POST['category'];
					$status = $_POST['status'];
					$username = $_POST['username'];
					$created = $_POST['created'];
					$this->components["pageitem"]->addPage($this->components["db"], $id, $title, $body, $category, $status, $username, $created);
     }

    function PagesDelPage()
    {
    $this->components['pageitem']->delPage($this->components['db'], $_POST['id']);
   	}

   	function PagesListPages()
   	{
	
	$items_per_page = 7;
	
	
   	$this->ui = '';
	
    isset($_POST['page']) ? $from_page = $_POST['page'] : $from_page = 1;
	
				$ordering = $_POST['ordering'];
                $filter_status = $_POST['filter_status'];
                $filter_category = $_POST['filter_category'];
                if ($filter_status == "") {$filter_status = "_any";};
                if ($ordering == "") {$ordering = "_any";};
                if ($filter_category == "") {$filter_category = "_any";};

				$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/adminpagelist.tpl');

                // Отбираемые поля
                $fields_set = " * ";
				
				
                $this->components['db']->setTable('pages');
			
// Получаем число записей в таблице
			
				$this->components['db']->Select("count(*) as totalitems ",
					$this->ListConditions($filter_status, $filter_category, $ordering)
					);
					
				$data = $this->components['db']->Read();
				
				$totalitems = $data['totalitems'];
				$total_pages = floor($totalitems/$items_per_page)+1;
				
// Переключатель страниц
				
				$paginator = 'Страницы : ';
				
				for ($p = ($from_page-2); $p<=($from_page+2); $p++)
				{
				if (($p>0) && ($p<=$total_pages))
				 $paginator .= "<span class='btn' onclick='gtpage($p)' style='cursor:pointer'>$p</span>&nbsp;&nbsp;";
				};
				
				
				$start_page = ($from_page - 1) * $items_per_page ;
				$this->components['db']->Select(" $fields_set ",
					$this->ListConditions($filter_status, $filter_category, $ordering) . " LIMIT $start_page,
					$items_per_page ");

				while ($data = $this->components['db']->Read())
				{
 				    $this->components['view']->SetVar('CAT', $data['category']);
					$this->components['view']->SetVar('PAGE-ID', $data['id']);
 				    $this->components['view']->SetVar('PAGE-TITLE', $data['title']);
 				    $this->components['view']->SetVar('PAGE-STATUS', $data['status']);
					$this->components['view']->SetVar('USERNAME', $data['username']);
					$this->components['view']->SetVar('CREATED', $data['created']);
					$this->components['view']->SetVar('VISITORS', $data['visitors']);
 				    $this->components['view']->SetVar('PAGE-LINK', '/content/'.$data['id']);
					$this->components['view']->CreateView();
					$this->ui .= $this->components['view']->getView();
				};


 			    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/pages.table.tpl');
 			    $this->components['view']->SetVar('VALUE_ORDERING', $ordering);
 			    $this->components['view']->SetVar('VALUE_FSTATUS', $filter_status);
 			    $this->components['view']->SetVar('VALUE_FCATEGORY', $filter_category);
				$this->components['view']->SetVar('ITEMS', $this->ui);
				
				$this->components['view']->SetVar('PAGE', $from_page);
				$this->components['view']->SetVar('PAGINATOR', $paginator);
				$this->components['view']->SetVar('TOTALPAGES', $total_pages);
				$this->components['view']->SetVar('PERPAGES', $items_per_page);
			
				
				


				$status_arr = array ('_any'=>'Все',
				'public' => 'опубликованные',
				'draft' =>'черновики',
				'bin' => 'в корзине'
				);

				$this->components['view']->SetVar('FILTER_STATUS',
					$this->components['formitems']->SelectItem(
						$status_arr,
						$filter_status,
						'filter_status_ui')
					);

				$categories_arr = $this->components['categoryitem']->getCategoriesArray(
								$this->components['db']
								);
				$categories_arr['_any'] = "любая";
				$this->components['view']->SetVar('FILTER_CATEGORY',
					$this->components['formitems']->SelectItem(
						$categories_arr,
						$filter_category,
						'filter_category_ui')
					);

				$ordering_arr = array ('_any'=>'несортированное',
				'date' => 'по дате',
				'status'=>'по статусу');

				$this->components['view']->SetVar('ORDERING',
				      $this->components['formitems']->SelectItem(
					    $ordering_arr,
						$ordering,
						'ordering_ui')
						);


				$this->components['view']->CreateView();
				$this->ui = $this->components['view']->GetView();
   		}

    function PagesTasks()
	{
	@isset ($_POST['mod_action']) ? $mod_action = $_POST['mod_action'] : $mod_action = '';
		switch ($_POST['mod_action'])
		{
// // // // // // //
// СТРАНИЦЫ // // //
// // // // // // //

                case 'clearbin'  :
                {
                	$this->PagesClearBin();
                	break;
                }
                ;
				case 'editpage'  :		// Вызов редактора для статьи
				{
                                $this->PagesEditPage();

								break;
				}


				;
					case 'savepage'  :	// Сохранение страницы
						{

					           $this->PagesSavePage();
					           $this->PagesListPages();
								break;
				}


				;

				case 'fillpage'   :
				{
				               $this->PagesFillPage();
								break;
				};

				case 'addpage' :
				{
					 $this->PagesAddPage();
					 $this->PagesListPages();
									break;
				}

				case 'delpage'   : // Удаление материала
				{
				               $this->PagesDelPage();
				               $this->PagesListPages();
								break;
				};
				
				case 'listpages'   : // Удаление материала
				{
				              $this->PagesListPages();
								break;
				};

				default :
				{
				$this->PagesListPages();
				break;
				};




		}
	}

	function ConfigureSite()
	{
switch ( $_POST['mod_action'] )
						{
// // // /// /// /// /// //  //
// ПОЛУЧЕНИЕ НАСТРОЕК САЙТА  //
// // /// // // /// /// /// ///


				// СМЕНА НАСТРОЕК
					case 'setcfg'      :
				{
$this->components['options']->setOption('SITE_NAME', $_POST['SITE_NAME']);
$this->components['options']->setOption('CACHE', $_POST['CACHE']);
$this->components['options']->setOption('META_DESCRIPTION', $_POST['META_DESCRIPTION']);
$this->components['options']->setOption('META_KEYWORDS', $_POST['META_KEYWORDS']);
$this->components['options']->setOption('EMAIL_ADMIN', $_POST['EMAIL_ADMIN']);
$this->components['options']->setOption('CLOSED', $_POST['CLOSED']);
$this->components['options']->setOption('MAIN_TEMPLATE', $_POST['MAIN_TEMPLATE']);
$this->components['options']->setOption('CLOSED_MESSAGE', $_POST['CLOSED_MESSAGE']);

$this->ui = 'Успешно сохранено! <a href="/admin/configure">Вернуться к настройкам</a>';
				break;
				};
				default :
				{
   			    $this->ui = '<h3>Глобальные настройки</h3><br/>';
				$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/globalconfig.tpl');
			    $arr = array ('ON'=>'Да', 'OFF'=>'Нет');
				$this->components['view']->SetVar('CACHE',
					$this->components['formitems']->SelectItem(
						$arr,
						$this->components['options']->getOption('CACHE'),
						'CACHE')
					);
				$this->components['view']->SetVar('SITE_NAME',$this->components['options']->getOption('SITE_NAME'));
				$this->components['view']->SetVar('MAIN_TEMPLATE',$this->components['options']->getOption('MAIN_TEMPLATE'));
  			    $this->components['view']->SetVar('META_DESCRIPTION',$this->components['options']->getOption('META_DESCRIPTION'));
  			    $this->components['view']->SetVar('META_KEYWORDS',$this->components['options']->getOption('META_KEYWORDS'));
  			    $this->components['view']->SetVar('EMAIL_ADMIN',$this->components['options']->getOption('EMAIL_ADMIN'));
                $arr = array ('ON'=>'Да', 'OFF'=>'Нет');
				$this->components['view']->SetVar('CLOSED',
					$this->components['formitems']->RadioItems(
						$arr,
						$this->components['options']->getOption('CLOSED'),
						'CLOSED')
					);
				$this->components['view']->SetVar('CLOSED_MESSAGE',$this->components['options']->getOption('CLOSED_MESSAGE'));
                $this->components['view']->SetVar('ACTION', 'setcfg');
				$this->components['view']->CreateView();
				$this->ui .= $this->components['view']->getView();
				break;
				};

	}
	}

// // // /// //
// КАТЕГОРИИ //
// // // // //


	function CategoriesTasks()
	{

$this->components['view']->SetVar('SYSTEM_MESSAGE', $_POST['mod_action'] );
		switch ($_POST['mod_action'])
		{

			    case 'filladdcat' :
				// Заполнение формы для добавления новой категории
				{
                $this->ui = '<h3>Внесение новой категории</h3><br/>';
				$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/structure.tpl');
				$this->components['view']->SetVar('CATEGORY', '');
				$arr = $this->components['categoryitem']->getCategoriesArray(
								$this->components['db']
								);
				$this->components['view']->SetVar('PARENT',
              			$this->components['formitems']->SelectItem(
								$arr,
								$this->components['pageitem']->category,
								'parent')
                );

                $this->components['view']->SetVar('CAT_NAME', '');
                $this->components['view']->SetVar('TEMPLATE', 'default');
                $this->components['view']->SetVar('ACTION', 'addcat');
				$this->components['view']->CreateView();
				$this->ui .= $this->components['view']->getView();
				break;
				}
			    case 'fillupdatecat' :
				// Заполнение формы для правки новой категории
				{
                $this->ui = '<h3>Правка категории</h3><br/>';
				$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/structure.tpl');
				$category = $_POST['category'];
				$this->components['categoryitem']->getCategory($this->components['db'], $category);
		        $this->components['view']->SetVar('CATEGORY', $this->components['categoryitem']->category);
		        $this->components['view']->SetVar('TEMPLATE', $this->components['categoryitem']->cat_template);
				$arr = $this->components['categoryitem']->getCategoriesArray(
								$this->components['db']
								);
				$this->components['view']->SetVar('PARENT',
              			$this->components['formitems']->SelectItem(
								$arr,
								$this->components['categoryitem']->cat_parent,
								'parent')
                );
                $this->components['view']->SetVar('CAT_NAME', $this->components['categoryitem']->cat_name);
                $this->components['view']->SetVar('ACTION', 'updatecat');
				$this->components['view']->CreateView();
				$this->ui .= $this->components['view']->getView();
					break;
				}
				case 'delcat'   :
				// Удаление существующей категории
				{
					$this->components['categoryitem']->deleteCategory($this->components['db'], $_POST['category']);
                    $this->ui = 'Успешно удалено! Страницы из данной категорий перемещены в корзину, а подкатегории прикреплены к узлу bin <br/><a href="/admin/categories">Вернуться к списку категорий</a> ';
					break;
				}
				case 'updatecat'   :
				// Обновление категории
				{
$this->components['categoryitem']->editCategory($this->components["db"], $_POST['category'], $_POST['parent'], $_POST['cat_name'], $_POST['template'], $_POST['old_category']);
$this->ui = 'Успешно обновлено! <a href="/admin/categories">Вернуться к списку категорий</a> ';
					break;
				}
				case 'addcat'   : {
			    // Добавление категории
			    $this->ui = 'Успешно сохранено! <a href="/admin/categories">Вернуться к списку категорий</a>';
				$Category = $_POST['category']; // Категория
				$Parent = $_POST['parent']; // Родительская
				$Cat_name = $_POST['cat_name']; // Название
				$aTemplate = $_POST['template']; // Шаблон
				$this->components['categoryitem']->addCategory($this->components["db"], $Category, $Parent, $Cat_name, $aTemplate);
				break;
				}
				default :
				// Просмотр категорий
				{
				$this->ui = '';
				$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/admincatlist.tpl');
				$this->components['db']->SetTable('categories');
				$this->components['db']->Select('category, cat_name, parent', '1=1');
				while ($data = $this->components['db']->Read())
				{
 				    $this->components['view']->SetVar('CATEGORY', $data['category']);
					$this->components['view']->SetVar('PARENT', $data['parent']);
 				    $this->components['view']->SetVar('CAT_NAME', $data['cat_name']);
					$this->components['view']->CreateView();
					$this->ui .= $this->components['view']->getView();
				};
   			    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/categories.table.tpl');
				$this->components['view']->SetVar('ITEMS', $this->ui);
				$this->components['view']->CreateView();
				$this->ui = $this->components['view']->GetView();

				break;
				}
		}
	}

	function CommentsTasks()
	{
				switch ($_POST['mod_action'])
					    {
					      case 'edit' : // Правка комментария
						{
								$this->ui = $this->components['comments']->com_updated();
								break;
						};
							case 'delete' : // Удаление комментария
						{
							$this->ui = $this->components['comments']->DelComment();
							break;
						};
						case 'accept' : // Одобрение комментария
						{
							$this->ui = $this->components['comments']->com_accepted($_POST['id_comment']);
							break;
						};
						case 'decline' : // Отклонение комментария
						{
							$this->ui = $this->components['comments']->com_declined($_POST['id_comment']);
							break;
						};
					    default :
					    {
					    	$this->ui = $this->components['comments']->admin_com_list();
							break;
						}
					}
		}
	
//	Управление глобальными блоками, наконец-то )
	function GlobalBlocks()
    {	
		switch ($_POST['mod_action'])
					    {
						case 'fillnew' : // Добавление глобального блока
						{
								$this->ui = $this->components['blocks']->fillnew();
								break;
						};
					      case 'filledit' : // Правка глобального блока
						{
								$this->ui = $this->components['blocks']->filledit();
								break;
						};
						case 'new' : // Добавление глобального блока, результат
						{
								$this->ui = $this->components['blocks']->newed();
								break;
						};
					      case 'edit' : // Правка глобального блока
						{
								$this->ui = $this->components['blocks']->edited();
								break;
						};
							case 'delete' : // Удаление глобального блока
						{
							$this->ui = $this->components['blocks']->delete();
							break;
						};						
					    default : // Список блоков
					    {
					    	$this->ui = $this->components['blocks']->view();
							break;
						}
					}
    }	

    // 
    // Надо подумать... а пока
    // include_once $_SERVER['DOCUMENT_ROOT'].'/classes/styles/to_halgerda.inc.php';
    
    
    //	Управление стилями
	function GlobalStyles()
    {	
		switch ($_POST['mod_action'])
					    {
						case 'fillnew' : // Добавление глобального блока
						{
								$this->ui = $this->components['styles']->fillnew();
								break;
						};
					      case 'filledit' : // Правка глобального блока
						{
								$this->ui = $this->components['styles']->filledit();
								break;
						};
						case 'new' : // Добавление глобального блока, результат
						{
								$this->ui = $this->components['styles']->newed();
								break;
						};
					      case 'edit' : // Правка глобального блока
						{
								$this->ui = $this->components['styles']->edited();
								break;
						};
							case 'delete' : // Удаление глобального блока
						{
							$this->ui = $this->components['styles']->delete();
							break;
						};						
					    default : // Список блоков
					    {
					    	$this->ui = $this->components['styles']->view();
							break;
						}
					}
    }

	// ПОЛУЧАЕТ ЗАДАЧУ ПО GET ЗАПРОСУ
	function getTask()
	{
	
		return $_GET['do'];
	}
	// ВЫПОЛНЯЕТ ПОСТАВЛЕННУЮ ЗАДАЧУ
	function action($aTask)
	{
			switch ($aTask)
			{

                            
  case 'styles' : // Глобальные стили
  {
    $this->GlobalStyles();
    break;
  };  
                            
            case 'blocks' : // Глобальные блоки
  {
    $this->GlobalBlocks();
    break;
  };  
            	case 'configure' : // Настройка сайта
            	{
            		$this->ConfigureSite(); break;     	}

    case 'comments' : // Просмотр комментариев
	{
     $this->CommentsTasks(); break;
   	}
    case 'categories' : // Управлание категориями
	{
		$this->CategoriesTasks(); break;
	};
    case 'pages' : // Управление страницами
	{
		$this->PagesTasks(); break;
	};
	
	
    
			} // end switch

	}
	// ОСНОВНОЙ ОБРАБОТЧИК СОБЫТИЙ
	function Run()
	{
		$this->action($this->getTask());
		return $this->ui;
	}



}

?>
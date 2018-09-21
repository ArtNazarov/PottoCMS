<?php
define('APP',1);
/*
 * Веб API
 * Сохранение данных
 * /classes/WebApi/WebApiTest.php?action=post&user=test&code=7&data=Example&crc=CRC
 * Пример ответа в JSON формате при успешном добавлении
 * {"result":true,"id":5}
 * Получение данных
 * /classes/WebApi/WebApiTest.php?action=get&user=test&id=1&code=7&crc=CRC
 * {"result":true,"data":{"id":"1","user":"test","data":"wow"}}
 * 
 * code должен содержать случайное число
 * чтобы сформировать подпись, необходимо
 * получить md5 хеш от соединения имени пользователя, его пароля и code
 * md5($user . $pass . $code);
 *  
 * таблица prefix__api_data
 * поле	Тип	Комментарий
 * id	int(11)	 номер записи
 * user	varchar(32) логин пользователя	 
 * data	text	данные пользователя
 * Индексы
 * PRIMARY	id
 * 
 * поле	Тип	Комментарий
 * user	varchar(32) пользователь	 
 * pass	varchar(32) пароль	 
 * Индексы
 * PRIMARY	user
 * 
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
  class WebApi {
      public $api_data = 'api_data';
      public $api_users = 'api_users';
      function __construct($params){
        $this->components = null;
        $this->components['factory'] = new ClassFactory($params);
        $this->components['db'] =  $this->components['factory']->createInstance("DatabaseLayer", $params, 'Core');
        $this->components['db']->Plug();
      }
      function getLastDataID(){
          $this->components['db']->setTable($this->api_data);
          $this->components['db']->Select('COUNT(*) as cnt', '1=1');
          $data = $this->components['db']->Read()[0];
          return $data['cnt']+1;
      }
      function PostData(){
        $user = $_GET['user'];
        $crc = $_GET['crc'];
        $code = $_GET['code'];
        $posted_data = $_GET['data'];
        if ($crc == $this->getRealCrc($user, $code)){
          $id = $this->getLastDataID();
          $this->components['db']->setTable($this->api_data);
          $this->components['db']->Insert("id, user, data", "$id, '$user', '$posted_data'");
          $result = ['result' => TRUE, 'id' => $id];
        } else { 
        $result = ['result' => FALSE, 'id' => -1];
        }
          header('Content-Type: application/json;charset=utf-8');
          echo json_encode($result);
      }
      function getRealCrc($user, $code){
        $this->components['db']->setTable($this->api_users);
        $this->components['db']->Select('*', "user='$user'");
        $data = $this->components['db']->Read()[0];
        $real_crc = md5($user . $data['pass'] . $code);
        return $real_crc;
      }
      function GetData(){
        $user = $_GET['user'];
        $crc = $_GET['crc'];
        $code = $_GET['code'];
        $id = $_GET['id'];
        if ($crc == $this->getRealCrc($user, $code)){
          $this->components['db']->setTable($this->api_data);
          $this->components['db']->Select('*', " (user='$user') AND (id=$id) ");
          $data = $this->components['db']->Read()[0];
           $result = ['result' => TRUE, 'data' => $data];
        }
         else {
           $result = ['result' => FALSE, 'data' => []];
         }
          header('Content-Type: application/json;charset=utf-8');
          echo json_encode($result);
      }
      function GetAllData(){
        $user = $_GET['user'];
        $crc = $_GET['crc'];
        $code = $_GET['code'];
        if ($crc == $this->getRealCrc($user, $code)){
          $this->components['db']->setTable($this->api_data);
          $this->components['db']->Select('*', "user='$user'");
          $data = $this->components['db']->Read();
           $result = ['result' => TRUE, 'data' => $data];
        }
         else {
           $result = ['result' => FALSE, 'data' => []];
         }
          header('Content-Type: application/json;charset=utf-8');
          echo json_encode($result);
      }
      function run(){
          isset($_GET['action']) ? $action = $_GET['action'] : $action = 'no_action';
          switch ($action){
              case 'get' : {$this->GetData(); break;}
              case 'post' : {$this->PostData(); break;}
              case 'get-all' : {$this->GetAllData(); break;}
              case 'no_action' : {
                  $result = ['result'=>FALSE, 'msg'=>'define ?action=param'];
                  header('Content-Type: application/json;charset=utf-8');
                  echo json_encode($result);
                  break;
              }
          }
      }
 }
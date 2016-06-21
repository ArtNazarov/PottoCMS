<?php
/*
CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `data` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32;
*/
define('APP', 0);
require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/Core/DatabaseLayer/DatabaseLayer.class.php');
$params = null;
class Model 
{
    var $id;
    var $data;
    var $db;
    var $table;
    
    function uses($d, $table)
    {
        $this->db = $d;
        $this->table = $table;
        
        $this->db->Plug();
        $this->db->setTable($this->table);
        
        
    }
    
    function begin()
    {
        
    }
    
    function end()
    {
             
    }
    
       
    function fetch_all($condition)
    {
    $this->begin();
    $array = [];
    $this->db->Select("*", $condition); // request 
    return $this->db->Read();
    }
    
    function save()
    {   
    $this->begin();
        if ($this->exists_self())
        {
          $this->db->Update("data='{$this->data}'", "id={$this->id}");
        }
        else
        {
          $this->db->Insert('id, data', "{$this->id}, '{$this->data}'");
        };
    $this->end();
    }
    
    function load()
    {        
        $obj = $this->db->Select(" * ", "id={$this->id}");
        $this->data = $obj[0]['data'];
    }
    
    function exists($condition)
    {
        $this->begin();
        $result = true;
        $this->db->Select("COUNT(*) as cnt", $condition);         
        $this->db->first();
        $rec = $this->db->readCursor();
        if ($rec['cnt'] == 0)
        {
          $result = false;
        };
        $this->end();
        return $result;
    }
    
    function exists_self()
    {
        return $this->exists("id={$this->id}");
    }
    
    function remove()
    {
        $this->begin();
        $this->db->Delete("id={$this->id}");
        $this->end();
    }
    
    function remove_if($condition)
    {
        $this->begin();
        $this->db->Delete($condition);
        $this->end();
    }
    
    
    
    
}

function render($message, $m)
{
echo "<h1>" . $message . "</h1>";
$rows = $m->fetch_all('1=1');
foreach ($rows as $i => $v)
{
    foreach ($v as $fieldName => $fieldValue)
    {
        echo $fieldName . '=' . $fieldValue . ' ';
    };
    echo "<br/>";
}

}

$params = null;
$db = new DatabaseLayer($params);
$m = new Model();
$m->uses($db, 'test');

$m->remove_if("1=1");
render('Clear database', $m);

$m->id = 1;
$m->data = 'Lorem ipsum dolor';
$m->save();

$m->id = 2;
$m->data = 'USSR comes back';
$m->save();

$m->id = 99;
$m->data = 'Dont worry';
$m->save();

render("After insertion", $m);

echo "<h1>Tests existing</h1>";
$m->id = 1;
if ( $m->exists_self() ) {echo "{$m->id} exists"; };
$m->id = 2;
if ( $m->exists_self() ) {echo "{$m->id} exists"; };
$m->id = 88;
if ( $m->exists_self() ) {echo "{$m->id} exists"; };



render('test fetch', $m); // test fetch
$m->id = 3;
$m->data = 'test 3';
$m->save(); // test save unexisted

render('test save unexisted', $m);

$m->id = 2;
$m->data = 'modify';
$m->save();

render('test save existed ', $m);

$m->remove(); // test delete

render('delete record', $m);

$m->id = 2;
$m->data = 'test2';
$m->save();

render('restore record', $m);

$m->id = 2;
$m->load(); // test load
echo $m->data;

















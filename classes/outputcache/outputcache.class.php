<?php
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/forest/forest.class.php';
class OutputCache
{
    var $cache;    
    var $key;
    var $log;
    var $has_cache = false; 
    function __construct($params)
        {
                 
            $this->cache = new Forest($params);
            $this->cache->path = $_SERVER['DOCUMENT_ROOT'].'/output_cache/';
            $this->cache->lifetime = 1;
            $request = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].$_SERVER['REQUEST_URI'];            
            $this->key = 'output_'.md5($request);
        }
        
    function bufon()
        {                         
            if ($this->cache->failed($this->key)==true) 
                {                                
                $this->has_cache = false;    
                ob_start();
                }
                else
                {
                $this->has_cache = true;
                };
         }

    function bufoff()
        {
            if ($this->has_cache == false)
            {
                	$buffer = ob_get_contents();
                        ob_end_flush();	                
                        $this->cache->save($this->key, $buffer);		                
                 }
                else
                {                        
	                $buffer = $this->cache->get_from_cache($this->key);
	                echo $buffer;
                };
        }
}
?>
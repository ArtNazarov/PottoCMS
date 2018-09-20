<?php
class EventHandlers
{
	var $hobj = null; // Ссылки на обработчик
	var $honevent = null; // Методы
	var $hcount = 0;
	function __construct($params)
	{
            echo "EventHandlers was created";
	}
	function add(&$ObsObject, $OnEvent)
	{
 	$this->hcount = $this->hcount + 1;
	$this->hobj[$this->hcount] = &$ObsObject;
	$this->honevent[$this->hcount] = $OnEvent;
	}
	function notify($event, $sender, $args)
	{
		for ($i=1; $i<=$this->hcount; $i++)
		{
		    $this->hobj[$i]->DispatchEvents($this->honevent[$i], $sender, $args);
		}
	}
}


?>
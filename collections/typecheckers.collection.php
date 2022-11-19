<?php

 function notNull(array $params, string $key){
     return ($params[$key]!=null);
 }
 
 function paramIsObject(array $params, string $key){
     return is_object($params[$key]);
 }
 
 function isExistedParam(array $params, string $key){
    return array_key_exists($key, $params);
 }
 
 

 function check_class(array $params , string $key, string $classname){
      // ключа в массиве нет
     if (!isExistedParam($params, $key)) return false;
     // ключ есть, проверим, что это не NULL
     if (!notNull($params, $key)) return false;
     // если не объект, то ошибка
     if (!paramIsObject($params, $key)) return false;
     // финальная проверка, проверим, что необходимого класса    
     return ( get_class($params[$key])==$classname); 
         
     }
 
 function shouldBe($params , string $key, string $classname){
     try {
     if (!check_class($params, $key, $classname)){
         
         throw new Error('Type mismatch' .$key. ' must be '. $classname );
         
        };
     }
     catch ( Exception $e) {
          echo $e->getMessage();
          debug_print_backtrace();
          
     };
     
     }    

<?php

namespace RpcExampleMethods;

class Test1 implements \Oploshka\Rpc\iMethod {
  
  public function description(){
    return <<<DESCRIPTION
Test description
DESCRIPTION;
  }
  
  public function validate(){
    return [];
  }
  
  public function run(&$_RESPONSE, $_DATA = array() ){
    $_RESPONSE->setData('test1::string', 'test string');
    $_RESPONSE->setData('test1::int', 1);
    $_RESPONSE->setLog('test1', 'testLog');
    $_RESPONSE->setError('ERROR_NOT');
  }
  
  public function return(){
    return [];
  }
  
}
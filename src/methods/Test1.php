<?php

namespace RpcMethods;

class Test1 implements \Oploshka\Rpc\Method {
  
  public function description(){
    return <<<DESCRIPTION
Test description
DESCRIPTION;
  }
  
  public function validate(){
    return [];
  }
  
  public function run(&$_RESPONSE, $_DATA = array() ){
    $_RESPONSE->infoAdd('test1::string', 'test string');
    $_RESPONSE->infoAdd('test1::int', 1);
    $_RESPONSE->logAdd('test1::testLog');
    $_RESPONSE->error('ERROR_NOT');
  }
  
}
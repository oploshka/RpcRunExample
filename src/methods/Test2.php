<?php

namespace RpcMethods;

class Test2 implements \Oploshka\Rpc\Method {
  
  public function description(){
    return <<<DESCRIPTION
Test description
DESCRIPTION;
  }
  
  public function validate(){
    return [
      's' => ['type'=>'string'],
    ];
  }
  
  public function run(&$_RESPONSE, $_DATA = array() ){
    $_RESPONSE->infoAdd('string', 'test string');
    $_RESPONSE->infoAdd('int', 1);
    $_RESPONSE->infoAdd('data', $_DATA);
    $_RESPONSE->error('ERROR_NOT');
  }
  
}
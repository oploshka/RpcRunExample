<?php

namespace RpcExample\MultipartPostJsonRpc;

class ReturnFormatter implements \Oploshka\Rpc\iReturnFormatter{
  
  private $Reform;
  
  function  __construct(){
    $this->Reform = new \Oploshka\Reform\Reform([]);
  }
  
  public function prepare($loadData, &$methodName, &$methodData) {
  
    if( !isset($methodData['method']) ) {
      return 'ERROR_NO_METHOD_NAME';
    }
    if( !isset($methodData['params']) ) {
      return 'ERROR_NO_METHOD_PARAMS';
    }
  
    // // todo: id, jsonrpc
    
    $methodName = $methodData['method'];
    $methodData = $methodData['params'];
    
    return 'ERROR_NOT';
    
  }
  public function format($methodName, $methodData, $Response, $ErrorStore) {
    
    $returnObj = [
      "jsonrpc" => "2.0",
      "error"   => $Response->getError(), // todo: {"code": -32700, "message": "Parse error"},
      'result'  => $Response->getData(),
      "id"      => null
    ];
    
    $log = $Response->getLog();
    if($log !== []){
      $returnObj['logs'] = $log;
    }
    
    $returnJson = $this->Reform->item($returnObj, ['type' => 'objToJson']);
    if($returnJson === NULL){
      $response = new \Oploshka\Rpc\Response();
      $response->setError('ERROR_CONVERT_RESPONSE_TO_JSON');
      return $this->format($methodName, $methodData, $Response, $ErrorStore);
    }
    return $returnJson;
  }
}
<?php

namespace RpcExample;


class MultipartPostJsonRpc {
  
  private $MethodStorage;
  private $Reform;
  
  public function __construct($MethodStorage, $Reform) {
    // TODO fix init
    $this->MethodStorage  = $MethodStorage;
    $this->Reform         = $Reform;
  }
  
  public function runDebug(){
    $debug = false;
    if( isset($_POST['debug']))
    { $debug = true; $debugTime = microtime(true);}
  
    $response = $this->run();
  
    if($debug) {
      $response->infoAdd('debug', [
        '_ScriptExecutionTime'    => ( microtime(true) - $debugTime ),
        // '_DATA'   => $_DATA, // WARNING !!! Not validate data
        '_GET'    => $_GET ,
        '_POST'   => $_POST,
      ]);
    }
    return $response;
  }
  
  // вытаскиваем данные откуда хотим:
  // возвращаем обьект при ошибке
  // [ true, 'String error code']
  public function getMultipartPostData($filed = 'data'){
    
    // Request method is post
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      return [true, 'ERROR_REQUEST_METHOD_TYPE'];
    }
    // Post is empty
    if($_POST == array() ) {
      return [true, 'ERROR_POST_NULL'];
    }
    // $_POST['data']  not send
    if( !isset($_POST[$filed]) ) {
      return [true, 'ERROR_POST_DATA_NULL'];
    }
    // convert $_POST['data'] (json string) in array
    $data = $this->Reform->item($_POST[$filed], ['type' => 'json']);
    if ($data === NULL){
      return [true, 'ERROR_POST_DATA_JSON_DECODE_ERROR'];
    }
    return [false, $data];
  }
  
  public function runJsonRpcV2($error, $data){
    $response = new \Oploshka\Rpc\Response();
    if($error){
      $response->error($data, false);
      return $response;
    }
    
    $id       = isset($data['id']) ? $data['id']            : false;
    $version  = isset($data['jsonrpc']) ? $data['jsonrpc']  : '';
    
    $methodName = isset($data['method']) ? $data['method'] : '';
    $methodData = isset($data['params']) ? $data['params'] : [];
    
    $RpcCore = new \Oploshka\Rpc\Core($this->MethodStorage, $this->Reform);
    $response   = $RpcCore->run($methodName, $methodData);
  }
  
  public function run(){
    
    $response = new \Oploshka\Rpc\Response();
    
    try {
      do {
        // Request method is post
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
          $response->error('ERROR_REQUEST_METHOD_TYPE', false);
          break;
        }
        // Post is empty
        if($_POST == array() ) {
          $response->error('ERROR_POST_NULL', false);
          break;
        }
        // $_POST['data']  not send
        if( !isset($_POST['data']) ) {
          $response->error('ERROR_POST_DATA_NULL', false);
          break;
        }
        // convert $_POST['data'] (json string) in array
        $_DATA = $this->Reform->item($_POST['data'], ['type' => 'json']);
        if ($_DATA === NULL){
          $response->error('ERROR_POST_DATA_JSON_DECODE_ERROR', false);
          break;
        }
        //
        $methodName = isset($_DATA['method']) ? $_DATA['method'] : '';
        $RpcCore = new \Oploshka\Rpc\Core($this->MethodStorage, $this->Reform);
        $response   = $RpcCore->run($methodName, $_DATA);
        
      } while(false);
    } catch (\Exception $e) {
      $response->logAdd( $e->getMessage() );
    }
    return $response;
  }
  
  public function responseToJson($response){
    $returnJson = $this->Reform->item($response->getResponse(), ['type' => 'objToJson']);
    if($returnJson === NULL){
      $response = new Response();
      $response->error('ERROR_CONVERT_RESPONSE_TO_JSON', false);
      $returnJson = $this->Reform->item($response->getResponse(), ['type' => 'objToJson']);
    }
    return $returnJson;
  }
}


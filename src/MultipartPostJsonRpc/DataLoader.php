<?php

namespace RpcExample\MultipartPostJsonRpc;


class DataLoader implements \Oploshka\Rpc\iDataLoader {

  private $filed ;
  private $Reform;
  
  function  __construct(){
    $this->filed  = 'data';
    $this->Reform = new \Oploshka\Reform\Reform([]);
  }
  
  public function load(&$methodName, &$methodData){
  
    // Request method is post
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      return 'ERROR_REQUEST_METHOD_TYPE';
    }
    // Post is empty
    if($_POST == [] ) {
      return 'ERROR_POST_NULL';
    }
    // $_POST['data']  not send
    if( !isset($_POST[$this->filed]) ) {
      return 'ERROR_POST_DATA_NULL';
    }
    // convert $_POST['data'] (json string) in array
    $data = $this->Reform->item($_POST[$this->filed], ['type' => 'json']);
    if ($data === NULL){
      return 'ERROR_POST_DATA_JSON_DECODE_ERROR';
    }
  
    // todo: id, jsonrpc
    if( !isset($data['method']) ) {
      return 'ERROR_NO_METHOD_NAME';
    }
    if( !isset($data['params']) ) {
      return 'ERROR_NO_METHOD_PARAMS';
    }

    $methodName = $data['method'];
    $methodData = $data['params'];
    
    return 'ERROR_NOT';
  }
  
}
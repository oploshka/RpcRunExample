<?php

namespace RpcExample;


class MultipartPostJsonRpc {
  
  
  // TODO: convert to Reform
  private function responseToJson($response){
    
    $returnJson = '';
    
    try {
      
      $returnJson = \json_encode(
        $response->getResponse(),
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT| JSON_PARTIAL_OUTPUT_ON_ERROR
      // http://php.net/manual/ru/json.constants.php
      );
      
      // обработки будут появлятся не смотря на установку option
      // по крайней мере что без JSON_PARTIAL_OUTPUT_ON_ERROR, что с ним будет json_last_error = 7
      // if (json_last_error() != 0 && json_last_error() != 7) {
      if (empty($returnJson)) {
        $response = new Response();
        try {
          $response->infoAdd('json_last_error', json_last_error());
          $response->error('ERROR_CONVERT_RESPONSE_TO_JSON');
        } catch (\Exception $e) {
          $returnJson = \json_encode(
            $response->getResponse(),
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT| JSON_PARTIAL_OUTPUT_ON_ERROR
          // http://php.net/manual/ru/json.constants.php
          );
        }
      }
    } catch (\Exception $e) {
      $response->logAdd( $e->getMessage() );
    }
    
    return $returnJson;
  }
  
  
  private $MethodStorage;
  private $Reform;
  
  public function __construct($MethodStorage, $Reform) {
    // TODO fix init
    $this->MethodStorage  = $MethodStorage;
    $this->Reform         = $Reform;
  }
  
  public function run(){
    
    $response = new \Oploshka\Rpc\Response();
    $returnJson = '';
    
    $debug = false; if( isset($_POST['debug'])) { $debug = true; $debugTime = microtime(true);}
    
    try {
      // Проверки для разных типов запросов
      if($_SERVER['REQUEST_METHOD'] !== 'POST'){ $response->error('ERROR_REQUEST_METHOD_TYPE'); }
      // пустой пост
      if($_POST == array() ) {  $response->error('ERROR_POST_NULL'); }
      // отсутствует data в пост запросе
      if( !isset($_POST['data']) ) {  $response->error('ERROR_POST_DATA_NULL'); }
      // преобразуем json в массив
      $_DATA = $this->Reform->item($_POST['data'], ['type' => 'json']);
      if ($_DATA === NULL){ $response->error('ERROR_POST_DATA_JSON_DECODE_ERROR' ); }
      $methodName = isset($_DATA['method']) ? $_DATA['method'] : '';
  
      $RpcCore = new \Oploshka\Rpc\Core($this->MethodStorage, $this->Reform);
      
      $response   = $RpcCore->run($methodName, $_DATA);
    } catch (\Exception $e) {
      $response->logAdd( $e->getMessage() );
    } finally {
      // debug info
      if($debug) {
        $response->infoAdd('debug', [
          '_ScriptExecutionTime'    => ( microtime(true) - $debugTime ),
          // '_DATA'   => $_DATA, // WARNING !!! Not validate data
          '_GET'    => $_GET ,
          '_POST'   => $_POST,
        ]);
      }
      $returnJson = $this->responseToJson($response);
    }
    
    return $returnJson;
  }
}


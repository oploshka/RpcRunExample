<?php

namespace RpcExample\MultipartPostJsonRpc;

class RpcWorker {
  
  private $MethodStorage;
  private $Reform;
  
  private $DataLoader;
  private $ReturnFormatter;
  
  public function __construct($MethodStorage, $Reform) {
    $this->MethodStorage  = $MethodStorage;
    $this->Reform         = $Reform;
    $this->DataLoader       = new DataLoader();
    $this->ReturnFormatter  = new ReturnFormatter();
  }
  
  public function run() {
    $RpcCore = new \Oploshka\Rpc\Core($this->MethodStorage, $this->Reform);
    $response = new \Oploshka\Rpc\Response();
    return $RpcCore->autoRun($response, $this->DataLoader, $this->ReturnFormatter, /*$ErrorStore*/ []);
  }
}
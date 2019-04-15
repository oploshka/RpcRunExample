<?php

namespace RpcExample;


class RpcServerCreate {

  private function getRpcReform(){
    return new \Oploshka\Reform\Reform();
  }
  private function getDataLoader(){
    return new \Oploshka\RpcDataLoader\PostMultipartFieldJson\DataLoaderPostMultipartFieldJson();
  }
  private function getReturnFormatter(){
    return new \Oploshka\RpcReturnFormatter\Json2\ReturnFormatterJson2();
  }
  private function getRpcMethodStorage(){
    $MethodStorage  = new \Oploshka\Rpc\MethodStorage();
    $MethodStorage->add('methodTest1', 'RpcExampleMethods\\Test1');
    $MethodStorage->add('methodTest2', 'RpcExampleMethods\\Test2');
    return $MethodStorage;
  }

  public function getRpcServer(){
    $MethodStorage    = $this->getRpcMethodStorage();
    $Reform           = $this->getRpcReform();
    $DataLoader       = $this->getDataLoader();
    $ReturnFormatter  = $this->getReturnFormatter();
    $ResponseClass = new \Oploshka\Rpc\Response();
    $Rpc = new \Oploshka\Rpc\Core($MethodStorage, $Reform, $DataLoader, $ReturnFormatter, $ResponseClass);
    $Rpc->applyPhpSettings();
    return $Rpc;
  }

}


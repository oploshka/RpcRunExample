<?php

namespace RpcExample;

use PHPUnit\Framework\TestCase;

class MultipartPostJsonRpcTest extends TestCase {
  
  private function getRpcServer(){
    $MethodStorage  = new \Oploshka\Rpc\MethodStorage();
    $Reform         = new \Oploshka\Reform\Reform();
    $MethodStorage->add('methodTest1', 'RpcMethods\\Test1');
    $MethodStorage->add('methodTest2', 'RpcMethods\\Test2');
    return new \RpcExample\MultipartPostJsonRpc($MethodStorage, $Reform);
  }
  
  public function testTodo() {
    $server = $this->getRpcServer();
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['data'] = '{"method":"methodTest1"}';
    $rpcResponse = $server->run();
    $rpcResponseArray = $rpcResponse->getResponse();
    $this->assertEquals( $rpcResponseArray['error'], 'ERROR_DEFAULT');
  }
}


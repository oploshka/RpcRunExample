<?php

namespace RpcExample\MultipartPostJsonRpc;

use PHPUnit\Framework\TestCase;

class RpcWorkerTest extends TestCase {
  
  private function getRpcServer(){
    $MethodStorage  = new \Oploshka\Rpc\MethodStorage();
    $Reform         = new \Oploshka\Reform\Reform();
    $MethodStorage->add('methodTest1', 'RpcMethods\\Test1');
    $MethodStorage->add('methodTest2', 'RpcMethods\\Test2');
    return new \RpcExample\MultipartPostJsonRpc\RpcWorker($MethodStorage, $Reform);
  }
  
  public function testTodo() {
    $server = $this->getRpcServer();
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['data'] = '{"method":"methodTest1", "params": []}';
    $rpcResponseJsonString = $server->run();
    $res = '{"jsonrpc":"2.0","error":"ERROR_NOT","result":{"test1::string":"test string","test1::int":1},"id":null,"logs":[{"test1":"testLog"}]}';
    $this->assertEquals( $rpcResponseJsonString, $res);
  }
}


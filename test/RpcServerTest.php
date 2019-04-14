<?php

namespace RpcExample;

use PHPUnit\Framework\TestCase;

class RpcWorkerTest extends TestCase {

  public function testWork() {
    $server = ( new \RpcExample\RpcServerCreate() )->getRpcServer();
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['data'] = '{"method":"methodTest1", "params": []}';
    $rpcResponseJsonString = $server->autoRun();
    $res = '{"jsonrpc":"2.0","error":"ERROR_NOT","result":{"test1::string":"test string","test1::int":1},"id":null,"logs":[{"test1":"testLog"}]}';
    $this->assertEquals( $rpcResponseJsonString, $res);
  }
}


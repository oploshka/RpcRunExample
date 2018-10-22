<?php

namespace RpcExample\MultipartPostJsonRpc;


class DataLoader implements \Oploshka\Rpc\iDataLoader {
  
  public function load(&$methodName, &$methodData){
    return 'ERROR_NOT';
  }
  
}
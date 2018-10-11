# Start Rpc Server example
composer.json file:
```json
{
  "name": "oploshka/rpc-example",
  "description": "",
  "license": "proprietary",
  "authors": [
    {
      "name": "Andrey Tyurin",
      "email": "ectb08@mail.ru"
    }
  ],
  "require": {
    "php": ">=7.0",
    "league/route": "^3.1",
    "zendframework/zend-diactoros": "^1.8",
    "oploshka/reform": "^0",
    "oploshka/rpc-core": "^0"
  },
  "require-dev": {
    "phpunit/phpunit": "^6"
  },
  "autoload": {
    "psr-4": {
      "RpcExample\\": "src",
      "RpcMethods\\": "src/methods"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "RpcExampleTest\\": "test",
      "RpcMethodsTest\\": "test/methods"
    }
  }
}
```

index.php file:
```php
<?php 

require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// fix League\Route
$_SERVER['REQUEST_URI'] = '/' . trim($_SERVER['REQUEST_URI'], '/');

$leagueContainer = new League\Container\Container;
$leagueContainer->share('response', Zend\Diactoros\Response::class);
$leagueContainer->share('request', function () {
  return Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
  );
});
$leagueContainer->share('emitter', Zend\Diactoros\Response\SapiEmitter::class);

$route = new League\Route\RouteCollection($leagueContainer);
$route
  ->map(['POST', 'GET'], '/', function (ServerRequestInterface $request, ResponseInterface $response) {
    // Init Rpc params
    $MethodStorage  = new \Oploshka\Rpc\MethodStorage();
    $Reform         = new \Oploshka\Reform\Reform();
    // init Rpc methods
    $MethodStorage->add('methodTest1', 'RpcMethods\\Test1');
    $MethodStorage->add('methodTest2', 'RpcMethods\\Test2');
    // Init Rpc Server
    $MultipartPostJsonRpc = new \RpcExample\MultipartPostJsonRpc($MethodStorage, $Reform);
    // Run Rpc
    $rpcResponse  = $MultipartPostJsonRpc->run();
    // Convert
    $returnJson   = $MultipartPostJsonRpc->responseToJson($rpcResponse);
    
    $response->getBody()->write($returnJson);
    return $response;
  })
  ->setStrategy(new \League\Route\Strategy\JsonStrategy());

$response = $route->dispatch($leagueContainer->get('request'), $leagueContainer->get('response'), $leagueContainer);
$leagueContainer->get('emitter')->emit($response);
```
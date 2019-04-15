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
    "oploshka/rpc-core": "^0.2",
    "oploshka/rpc-data-loader-post-multipart-field-json": "^0.2",
    "oploshka/rpc-return-formatter-json2": "^0.2"
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
    $MethodStorage->add('methodTest1', 'RpcExampleMethods\\Test1');
    $MethodStorage->add('methodTest2', 'RpcExampleMethods\\Test2');
    
    $Reform           = new \Oploshka\Reform\Reform();
    $DataLoader       = new \Oploshka\RpcDataLoader\PostMultipartFieldJson\DataLoaderPostMultipartFieldJson();
    $ReturnFormatter  = new \Oploshka\RpcReturnFormatter\Json2\ReturnFormatterJson2();
    $ResponseClass = new \Oploshka\Rpc\Response();
    
    $Rpc = new \Oploshka\Rpc\Core($MethodStorage, $Reform, $DataLoader, $ReturnFormatter, $ResponseClass);
    $Rpc->applyPhpSettings();
    
    $returnJson   = $Rpc->autoRun();
    
    $response->getBody()->write($returnJson);
    return $response;
  })
  ->setStrategy(new \League\Route\Strategy\JsonStrategy());

$response = $route->dispatch($leagueContainer->get('request'), $leagueContainer->get('response'), $leagueContainer);
$leagueContainer->get('emitter')->emit($response);
```
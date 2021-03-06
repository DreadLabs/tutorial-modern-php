<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use ExampleApp\HelloWorld;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Narrowspark\HttpEmitter\SapiEmitter;
use Relay\Relay;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions(
    [
        HelloWorld::class => \DI\create(HelloWorld::class)->constructor(
            \DI\get('Foo'),
            \DI\get('ResponsePrototype')
        ),
        'Foo' => 'bar',
        'ResponsePrototype' => function() {
            return new Response();
        },
    ]
);

$container = $containerBuilder->build();

$routes = \FastRoute\simpleDispatcher(
    function (RouteCollector $r) {
        $r->get('/hello[/{name}]', HelloWorld::class);
    }
);

$middlewareQueue = [];
$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);

$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
$emitter->emit($response);

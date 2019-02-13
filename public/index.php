<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use ExampleApp\HelloWorld;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Relay\Relay;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(false);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions(
    [
        HelloWorld::class => \DI\create(HelloWorld::class)->constructor(\DI\get('Foo')),
        'Foo' => 'bar',
    ]
);

$container = $containerBuilder->build();

$routes = \FastRoute\simpleDispatcher(
    function (RouteCollector $r) {
        $r->get('/hello', HelloWorld::class);
    }
);

$middlewareQueue = [];
$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);

$requestHandler = new Relay($middlewareQueue);
$requestHandler->handle(\Zend\Diactoros\ServerRequestFactory::fromGlobals());

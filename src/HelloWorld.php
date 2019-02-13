<?php
declare(strict_types=1);

namespace ExampleApp;

use Psr\Http\Message\ResponseInterface;

class HelloWorld
{
    /**
     * @var string
     */
    private $foo;

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(string $foo, ResponseInterface $response)
    {
        $this->foo = $foo;
        $this->response = $response;
    }

    public function __invoke(): ResponseInterface
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(sprintf('<html><head></head><body>Hello, %s world!</body>', $this->foo));

        return $response;
    }
}

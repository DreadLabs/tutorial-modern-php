<?php
declare(strict_types=1);

namespace ExampleApp;

class HelloWorld
{
    /**
     * @var string
     */
    private $foo;

    public function __construct(string $foo)
    {
        $this->foo = $foo;
    }

    public function __invoke(): void
    {
        echo sprintf('Hello, %s world!', $this->foo);

        exit;
    }
}

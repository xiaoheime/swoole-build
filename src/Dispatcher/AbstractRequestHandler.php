<?php

namespace Build\Dispatcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractRequestHandler implements RequestHandlerInterface
{

    protected MiddlewareInterface $coreHandler;
    protected array $middlewares = [];
    protected int $offset = 0;

    public function __construct(array $middlewares, MiddlewareInterface $coreHandler)
    {
        $this->middlewares = $middlewares;
        $this->coreHandler = $coreHandler;
    }

    protected function handleRequest($request): ResponseInterface
    {
        if (!isset($this->middlewares[$this->offset]) && !empty($this->coreHandler)) {
            $handler = $this->coreHandler;
        } else {
            $handler = $this->middlewares[$this->offset];
            is_string($handler) && $handler = new $handler();
        }
        if (!method_exists($handler, 'process')) {
            throw new \InvalidArgumentException(sprintf('Invalid middleware, it has to provide a process() method.'));
        }
        return $handler->process($request, $this->next());
    }

    protected function next(): self
    {
        ++$this->offset;
        return $this;
    }
}
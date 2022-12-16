<?php

namespace Build\HttpServer;

use Build\Config\ConfigFactory;
use Build\Dispatcher\HttpRequestHandler;
use Build\HttpServer\Route\Dispatched;
use Build\HttpServer\Route\DispatcherFactory;
use FastRoute\Dispatcher;
use Hyperf\HttpMessage\Server\Connection\SwooleConnection;
use Hyperf\HttpMessage\Server\Request as Psr7Request;
use Hyperf\HttpMessage\Server\Response as Psr7Response;
use Hyperf\Utils\Context;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpServer
{

    protected Dispatcher $dispatcher;
    protected CoreMiddleware $coreMiddleware;

    protected array $globalMiddlewares;
    protected DispatcherFactory $dispatcherFactory;

    public function __construct(DispatcherFactory $dispatcherFactory)
    {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->dispatcher = $dispatcherFactory->getDispatcher('http');

    }

    public function initCoreMiddleware()
    {
        $this->coreMiddleware = new CoreMiddleware($this->dispatcherFactory);
        $config = (new ConfigFactory())();
        $this->globalMiddlewares = $config->get('middlewares');
    }

    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {

        /** @var RequestInterface $psr7Request */
        /** @var ResponseInterface $psr7Response */
        [$psr7Request, $psr7Response] = $this->initRequestAndResponse($request, $response);
        $psr7Request = $this->coreMiddleware->dispatch($psr7Request);

        $httpMethod = $psr7Request->getMethod();
        $path = $psr7Request->getUri()->getPath();

        $middlewares = $this->globalMiddlewares;
        $dispatched = $psr7Request->getAttribute(Dispatched::class);
        if ($dispatched instanceof Dispatched && $dispatched->isFound()) {
            $registerMiddlewares = MiddlewareManger::get($path, $httpMethod);
            $middlewares = array_merge($middlewares, $registerMiddlewares);
        }

        $requestHandle = new HttpRequestHandler($middlewares, $this->coreMiddleware);
        $psr7Response = $requestHandle->handle($psr7Request);
        /*
        * Headers
        */
        foreach ($psr7Response->getHeaders() as $key => $value) {
            $response->header($key, implode(';', $value));
        }

        /*
         * Status code
         */
        $response->status($psr7Response->getStatusCode());
        $response->end($psr7Response->getBody()->getContents());
    }

    /**
     * Initialize PSR-7 Request and Response objects.
     * @param mixed $request swoole request or psr server request
     * @param mixed $response swoole response or swow connection
     */
    protected function initRequestAndResponse($request, $response): array
    {
        Context::set(ResponseInterface::class, $psr7Response = new Psr7Response());

        if ($request instanceof ServerRequestInterface) {
            $psr7Request = $request;
        } else {
            $psr7Request = Psr7Request::loadFromSwooleRequest($request);
            $psr7Response->setConnection(new SwooleConnection($response));
        }

        Context::set(ServerRequestInterface::class, $psr7Request);
        return [$psr7Request, $psr7Response];
    }
}
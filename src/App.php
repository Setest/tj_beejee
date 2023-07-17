<?php

declare(strict_types=1);

namespace App;

use http\Client\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * @property \Psr\Http\Message\ResponseFactoryInterface $response
 */
class App
{
    public function __construct(
        private \Psr\Http\Message\ResponseFactoryInterface $responseFactory,
        private \Symfony\Component\HttpFoundation\Request  $request,
        private \Symfony\Component\Routing\Router          $router,
        private bool                                       $debug = false,
    ) {
        // TODO should be controlled by service
        session_start();
    }

    private function isDebug(){
        return $this->debug;
    }

    /**
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        if ($this->isDebug()){
            ini_set('html_errors', 1);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }

        try {
            $handlerMeta = $this->router->matchRequest($this->request);
            
            /** @var string $controller */
            $controller = $handlerMeta['_controller'];
            /** @var string $controller */
            $action = $handlerMeta['_action'];
            $loader = new FilesystemLoader(__DIR__ . '/View');
            $twig = new Environment($loader);

            /** @var \App\Controller\BaseController $handler */
            $handler = new $controller($twig, $request, $this->responseFactory, $this->router);
            $handler->setRouteHandlerName($handlerMeta['_route']);

            $reflector = new \ReflectionClass($controller);
            $method = $reflector->getMethod($action);

            $arguments  = [];
            $parameters = $method->getParameters();
            foreach ($parameters as $param) {
                // TODO add DI by interfaces and map
                
                $name = $param->getName();

                if ($name === '__params') {
                    $arguments[$name] = $handlerMeta;
                    continue;
                }

                if (isset($handlerMeta[$name])) {
                    if ($type = $param->getType()){
                        settype($handlerMeta[$name], $type->getName());
                    }

                    $arguments[$name] = $handlerMeta[$name];
                    continue;
                }

                if ($param->isDefaultValueAvailable()) {
                    $arguments[$name] = $param->getDefaultValue();
                    continue;
                }

                throw new \Exception("Argument '{$name}' is not set and have not default value");
            }

            $response = $method->invokeArgs($handler, $arguments);
        } catch (ResourceNotFoundException) {
            $response = $this->responseFactory->createResponse(404, 'Handler not found');
        } catch (\Throwable) {
            $response = $this->responseFactory->createResponse(500, 'Internal server error');
        }

        session_write_close();
        return $response;
    }
}

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

            $response = (new $controller($twig, $request, $this->responseFactory))->$action();
        } catch (ResourceNotFoundException) {
            $response = $this->responseFactory->createResponse(404, 'Handler not found');
        } catch (\Throwable) {
            $response = $this->responseFactory->createResponse(500, 'Internal server error');
        }

        return $response;
    }
}

<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @property \Psr\Http\Message\ResponseFactoryInterface $response
 */
class App
{
    private \Psr\Http\Message\ResponseFactoryInterface $response;
    private \Symfony\Component\HttpFoundation\Request $request;
    private \Symfony\Component\Routing\Router $router;
    
    public function __construct(\Psr\Http\Message\ResponseFactoryInterface $response, \Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\Routing\Router $router) {
        $this->response = $response;
        $this->request  = $request;
        $this->router   = $router;
    }
    
    /**
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        try {
            $handlerMeta = $this->router->matchRequest($this->request);
            
            /** @var string $controller */
            $controller = $handlerMeta['_controller'];
            /** @var string $controller */
            $action = $handlerMeta['_action'];
            
            $response = (new $controller)->$action($request);
        } catch (ResourceNotFoundException $e) {
            $response = $this->response->createResponse(404, 'Handler not found');
        } catch (\Throwable $e) {
            $response = $this->response->createResponse(500, 'Internal server error');
        }
        
        return $response;
    }
}

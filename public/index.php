<?php

declare(strict_types=1);

use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Nyholm\Psr7\Factory\Psr17Factory;

require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$appRoot = dirname(__DIR__);

$symfonyRequest = Symfony\Component\HttpFoundation\Request::createFromGlobals();

$router = new Router(
    new YamlFileLoader(new FileLocator([$appRoot . '/config'])), 'routes.yaml', ['cache_dir' => $appRoot . '/var/cache']
);

$router->setContext((new RequestContext())->fromRequest($symfonyRequest));

$psr17Factory = new Psr17Factory();
$httpFactory  = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

try {
    $handlerMeta = $router->matchRequest($symfonyRequest);
    
    /** @var string $controller */
    $controller = $handlerMeta['_controller'];
    /** @var string $controller */
    $action = $handlerMeta['_action'];
    
    $symfonyRequest = Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $request        = $httpFactory->createRequest($symfonyRequest);
    $response       = (new $controller)->$action($request);
} catch (ResourceNotFoundException $e) {
    $response = $psr17Factory->createResponse(404, 'Handler not found');
} catch (\Throwable $e) {
    $response = $psr17Factory->createResponse(500, 'Internal server error');
}

(new HttpFoundationFactory())->createResponse($response)->send();

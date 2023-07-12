<?php

declare(strict_types=1);

use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Nyholm\Psr7\Factory\Psr17Factory;
use App\App;

require __DIR__ . '/../vendor/autoload.php';

$appRoot = dirname(__DIR__);

$symfonyRequest = Symfony\Component\HttpFoundation\Request::createFromGlobals();

$router = new Router(
    new YamlFileLoader(new FileLocator([$appRoot . '/config'])), 'routes.yaml', ['cache_dir' => $appRoot . '/var/cache']
);

$router->setContext((new RequestContext())->fromRequest($symfonyRequest));

$psr17Factory = new Psr17Factory();
$httpFactory  = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

$symfonyRequest = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$request        = $httpFactory->createRequest($symfonyRequest);

$debugMode = !!getenv('APP_ENV');

$app = new App($psr17Factory, $symfonyRequest, $router, $debugMode);
(new HttpFoundationFactory())->createResponse($app->handle($request))->send();
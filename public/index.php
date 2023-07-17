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

$database = new \Medoo\Medoo([
    'type' => 'sqlite',
    'database' => 'file://' . $appRoot . '/' . (getenv('DATABASE_PATH') ?: 'db/data/db.db') . '?mode=rw',
]);

// I know its stupid and simple way to create connection with DB, dont blame me )
\App\Helper\Db::getInstance($database);

//$x = \App\Model\Task::doneToggle(10);
//var_dump($x);

//$database->beginDebug();
//$z = $database->select('tasks', '*', ['id' => 10]);
//$x = \App\Model\Task::create('xxx','yyy', "' LIMIT 1```\"");
//$z = \App\Model\Task::findAll();
//var_dump($z);
//var_dump($database->debugLog());

$app = new App($psr17Factory, $symfonyRequest, $router, $debugMode);
(new HttpFoundationFactory())->createResponse($app->handle($request))->send();
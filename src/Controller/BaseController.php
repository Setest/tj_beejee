<?php

namespace App\Controller;

//use Psr\Http\Message\ResponseInterface as ResponseInterfaceAlias;
//use Psr\Http\Message\ServerRequestInterface as ServerRequestInterfaceAlias;

interface BaseController
{
    public function render(string $name, array $opts = []): string;
    
    public function getRouteHandlerName(): string;
    
    /**
     * @param string $routeHandlerName
     */
    public function setRouteHandlerName(string $routeHandlerName);
}
<?php

namespace App\Controller;

use Pagerfanta\Adapter\NullAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap5View;
use Psr\Http\Message\ServerRequestInterface as ServerRequestInterfaceAlias;

abstract class AbstractController implements BaseController{
    private string $routeHandlerName;
    
    const ORDER_URL_OPTION_NAME = 'order';
    const DIRECTION_URL_OPTION_NAME = 'dir';
    
    public function __construct(
        private \Twig\Environment                          $twig,
        private ServerRequestInterfaceAlias                $request,
        private \Psr\Http\Message\ResponseFactoryInterface $responseFactory,
        private \Symfony\Component\Routing\Router          $router,
    )
    {
    }
    
    /**
     * @return \Symfony\Component\Routing\Router
     */
    public function getRouter(): \Symfony\Component\Routing\Router {
        return $this->router;
    }
    
    /**
     * @return mixed
     */
    public function getRouteHandlerName(): string {
        return $this->routeHandlerName;
    }
    
    /**
     * @param string $routeHandlerName
     */
    public function setRouteHandlerName(string $routeHandlerName): void {
        $this->routeHandlerName = $routeHandlerName;
    }
    
    protected function getRequest(): ServerRequestInterfaceAlias
    {
        return $this->request;
    }
    
    /**
     * Generate rendered pagination
     *
     * @param int    $page
     * @param int    $total
     * @param int    $limit
     * @param string $link
     *
     * @return string
     */
    protected function pagination(int $page, int $total, int $limit, array $routeOpts = []): string
    {
        $adapter    = new NullAdapter($total);
        $pagerfanta = new Pagerfanta($adapter);
        
        $pagerfanta->setMaxPerPage($limit)->setCurrentPage($page);
        
        return (new TwitterBootstrap5View())->render($pagerfanta,
            fn($pageNum) => $this->generatePaginationUrl($pageNum, $routeOpts),
            [
                'css_container_class' => 'pagination justify-content-center',
            ]
        );
    }
    
    private function generatePaginationUrl(int $pageNum, array $routeOpts = []): string
    {
        return $this->getRouter()->generate(
            $this->getRouteHandlerName(),
            [...$routeOpts, 'page' => $pageNum]);
    }
    
    public function render(string $name, array $opts = []): string
    {
        return $this->twig->render($name, $opts);
    }

    public function renderAndReturnResponse(string $name, array $opts = [], int $status = 200)
    {
        $pages = [];
        // TODO put in cache
        $routeInfo = $this->router->match($this->request->getUri()->getPath());
        foreach ($this->router->getRouteCollection() as $routeName => $col) {
            if (in_array('GET', $col->getMethods() ) && $col->getDefault('title')){
                $pages[] = [
                    'link'   => ($routeInfo['_route'] === $routeName)
                        ? $this->request->getUri()->getPath()
                        : $this->router->generate($routeName),
                    'title'  => $col->getDefault('title'),
                    'active' => ($routeInfo['_route'] === $routeName)
                ];
            }
        }
        
        $opts = [
            'pagination' => '',
            self::ORDER_URL_OPTION_NAME => 'id',
            self::DIRECTION_URL_OPTION_NAME => 'DESC',
            'query' => [
                'params' => $this->getRequest()->getQueryParams()
            ],
            'pages' => $pages,
            ...$opts
        ];

        $result = $this->render($name, $opts);

        return $this->createResponse($status, $result);
    }

    public function createResponse(int $status, $body)
    {
        $response = $this->responseFactory->createResponse($status);
        $responseBody = $this->responseFactory->createStream($body);
        return $response->withBody($responseBody);
    }
}
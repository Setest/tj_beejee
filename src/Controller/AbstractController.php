<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as ServerRequestInterfaceAlias;

abstract class AbstractController implements BaseController{
    public function __construct(
        private \Twig\Environment                          $twig,
        private ServerRequestInterfaceAlias                $request,
        private \Psr\Http\Message\ResponseFactoryInterface $responseFactory,
    )
    {
    }

    protected function getRequest(): ServerRequestInterfaceAlias
    {
        return $this->request;
    }

    public function render(string $name, array $opts = []): string
    {
        return $this->twig->render($name, $opts);
    }

    public function renderAndReturnResponse(string $name, array $opts = []){
        $opts = [
            'currentPage' => $this->request->getRequestTarget(),
            'pages' => [
                ['link'=>'/', 'title' => 'Home'],
                ['link'=>'/tasks', 'title' => 'Tasks'],
                ['link'=>'/auth', 'title' => 'Login'],
            ],
            ...$opts];

        $result = $this->render($name, $opts);

        return $this->createResponse(200, $result);
    }

    public function createResponse(int $status, $body){
        $response = $this->responseFactory->createResponse($status);
        $responseBody = $this->responseFactory->createStream($body);
        return $response->withBody($responseBody);
    }
}
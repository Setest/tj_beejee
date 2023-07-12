<?php

namespace App\Controller;

class Home extends AbstractController {
    public function handle()
    {
        return $this->renderAndReturnResponse('home.html.twig');
    }
}
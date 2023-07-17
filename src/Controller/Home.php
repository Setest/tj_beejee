<?php

namespace App\Controller;

use App\Helper\Auth;

class Home extends AbstractController {
    public function handle()
    {
        
        $user = [];
        if (Auth::isUserLoggedIn()){
            $user = Auth::getLoggedUser();
        }
        
        return $this->renderAndReturnResponse('home.html.twig', [
            'user' => $user
        ]);
    }
}
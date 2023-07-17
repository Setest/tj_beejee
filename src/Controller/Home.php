<?php

namespace App\Controller;

use App\Helper\Auth;

class Home extends AbstractController {
    public function handle()
    {
        
        $user = [];
        if (Auth::isUserAuthorized()){
            $user = Auth::getLoggedUser();
        }
        
        return $this->renderAndReturnResponse('home.html.twig', [
            'user' => $user
        ]);
    }
}
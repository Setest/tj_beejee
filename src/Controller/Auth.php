<?php

namespace App\Controller;

use App\Model\User;

class Auth extends AbstractController
{
    public function handle() {
        return $this->renderAndReturnResponse('auth.html.twig');
    }
    
    public function login() {
        $data = $this->getRequest()->getParsedBody();
        
        if (!User::hasByLoginAndPassword($data['login'], $data['password'])) {
            return $this->returnJsonResponse(
                [
                    'status'  => false,
                    'message' => 'Login or password incorrect',
                ],
                500
            );
        }
        
        \App\Helper\Auth::auth(User::findByLogin($data['login']));
        
        return $this->returnJsonResponse(
            [
                'status'  => true,
                'message' => '',
            ],
            200
        );
    }
    
    public function logout() {
        \App\Helper\Auth::logout();
        
        return $this->redirectTo('index');
    }
}
<?php

declare(strict_types=1);

namespace App\Helper;

class Auth
{
    public static function isUserAuthorized(): bool {
        return !!($_SESSION['user'] ?? false);
    }
    
    public static function getLoggedUser(): array {
        return $_SESSION['user'] ?? [];
    }
    
    public static function logout() {
//        session_destroy();
        unset ($_SESSION['user']);
    }
    public static function auth(array $userData) {
        $_SESSION['user'] = $userData;
    }
}
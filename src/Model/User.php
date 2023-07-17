<?php

namespace App\Model;

use App\Helper\Db;

class User extends AbstractActiveRecord implements ActiveRecord
{
    private const HASH_PASSWORD_ALG = 'sha256';
    public static function getTableName(): string
    {
        return 'user';
    }

    /**
     * @return array
     */
    public static function getFields(): array
    {
        return [
            'id',
            'username',
            'password',
            'created_at',
            'updated_at',
        ];
    }
    
    public static function findByLogin(string $login){
        return self::findByFieldVal('username', $login);
    }
    
    public static function hasByLoginAndPassword(string $username, string $password): bool {
        $user = self::findByLogin($username);
        if (!$user){
            return false;
        }
        
        return $user['password'] === self::getPasswordHash($password);
    }
    
    private static function getPasswordHash($password):string
    {
        $password = (string)$password;
        
        return hash(self::HASH_PASSWORD_ALG, $password);
    }
    
    /**
     * @param string $username
     * @param string $email
     * @return bool
     */
    public static function create(string $username, string $email, string $content): bool
    {
        return !!Db::$connection->insert(self::getTableName(), [
            'username' => $username,
            'email' => $email,
            'content' => $content,
            'created_at' => time(),
        ]);
    }
}
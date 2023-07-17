<?php

namespace App\Model;

use App\Helper\Db;

class Task extends AbstractActiveRecord implements ActiveRecord
{
    public static function getTableName(): string
    {
        return 'task';
    }

    /**
     * @return array
     */
    public static function getFields(): array
    {
        return [
            'id',
            'username',
            'email',
            'content',
            'done',
            'created_at',
            'updated_at',
            'edited_at',
        ];
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

    /**
     * @param int $id
     * @param string $username
     * @param string $email
     * @param string $content
     * @return bool
     */
    public static function update(int $id, string $username, string $email, string $content, bool $done, ?int $editedAt): bool
    {
        return !!Db::$connection->update(self::getTableName(), [
            'username' => $username,
            'email' => $email,
            'content' => $content,
            'done' => $done,
            'updated_at' => time(),
            'edited_at' => $editedAt,
        ], ['id' => $id]);
    }
    
    public static function insert(string $username, string $email, string $content): bool
    {
        return !!Db::$connection->insert(self::getTableName(), [
            'username' => $username,
            'email' => $email,
            'content' => $content,
            'updated_at' => time(),
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function doneToggle(int $id): bool
    {
        $done = Db::$connection->get(self::getTableName(), 'done', ['id' => $id]);

        return !!Db::$connection->update(self::getTableName(), [
            'done' => !$done,
            'updated_at' => time(),
        ], ['id' => $id]);
    }

}
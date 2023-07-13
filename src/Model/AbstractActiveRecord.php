<?php

namespace App\Model;

use App\Helper\Db;

abstract class AbstractActiveRecord
{
    abstract static public function getTableName(): string;

    abstract static public function getFields(): array;

    /**
     * @param int $limit
     * @param int $offset
     * @param string $orderField
     * @param string $orderDirection
     * @return array|null
     */
    public static function findAll(int $limit = 10, int $offset = 0, string $orderField = 'id', string $orderDirection = 'DESC')
    {
        $orderField = in_array($orderField, static::getFields()) ? $orderField : 'id';
        $orderDirection = $orderDirection === 'DESC' ? 'DESC' : 'ASC';

        return Db::$connection->select(static::getTableName(), "*", [
            "ORDER" => [
                $orderField => $orderDirection,
            ],
            'LIMIT' => [$offset, $limit],
        ]);
    }


}
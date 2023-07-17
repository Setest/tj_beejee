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
        $orderField = strtolower($orderField);
        $orderField = in_array($orderField, static::getFields()) ? $orderField : 'id';
        $orderDirection = strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';

        return Db::$connection->select(static::getTableName(), "*", [
            "ORDER" => [
                $orderField => $orderDirection,
            ],
            'LIMIT' => [$offset, $limit],
        ]);
    }
    
    public static function findByFieldVal(string $field, $val)
    {
        return Db::$connection->get(static::getTableName(), "*", [
            $field => $val,
        ]);
    }
    
    public static function filterOrderFieldByName(string $orderField = 'id'): string
    {
        $orderField = strtolower($orderField);
        return in_array($orderField, static::getFields()) ? $orderField : 'id';
    }
    
    public static function filterOrderDirectionByName(string $orderDirection = 'DESC'): string
    {
        return strtoupper($orderDirection) === 'DESC' ? 'DESC' : 'ASC';
    }
    
    public static function count()
    {

        return Db::$connection->count(static::getTableName());
    }


}
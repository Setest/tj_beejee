<?php

namespace App\Model;

use App\Helper\Db;

interface ActiveRecord
{
    public static function getTableName(): string;

    public static function getFields(): array;
}
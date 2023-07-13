<?php

namespace App\Helper;

use Medoo\Medoo as MedooAlias;

final class Db
{
    private static $_instance;

    private function __construct()
    {
    }

    protected function __clone()
    {
    }

    public static MedooAlias $connection;

    static public function getInstance($connection)
    {
        if (is_null(self::$_instance)) {
            self::$connection = $connection;
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}

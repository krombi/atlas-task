<?php
namespace DB;

use Helpers\Interfaces\IDataBase;

class Distributor
{

    /**
     * хранилище объектов баз данных
     */
    public static $industry = [];

    /**
     * возможные базы данных
     */
    private static $db_list = ['MySQL'];

    /**
     * 
     */
    public static function init(string $db = 'MySQL'): ?IDataBase
    {

        if (in_array($db, self::$db_list)) {

            $db_class = implode("\\", [
                __NAMESPACE__,
                $db
            ]);

            $var = mb_strtolower($db);

            self::$industry[$db] = new $db_class();

            return self::$industry[$db];

        }

        return null;

    }

    /**
     * 
     */
    public static function get(string $db): ?IDataBase
    {

        if (isset(self::$industry[$db]) && self::$industry[$db] instanceof IDataBase) {

            return self::$industry[$db];

        }

        return null;

    }

}
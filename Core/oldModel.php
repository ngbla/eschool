<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */

    protected static function getDB(){
        static $db = null;
        
        if ($db === null) {


            if ($_SERVER['HTTP_HOST'] == 'localhost.eschool') {
                //var_dump($_SERVER['HTTP_HOST']);
                $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
                $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
            }
            else {
                $dsn = 'mysql:host=' . Config::DB_HOST_ONLINE . ';dbname=' . Config::DB_NAME_ONLINE . ';charset=utf8';
                $db = new PDO($dsn, Config::DB_USER_ONLINE, Config::DB_PASSWORD_ONLINE);
            }
            
            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }
}

<?php

namespace App\Core;

class DBConnection
{
    static $pdo = NULL;

    public function __construct()
    {
        self::$pdo =  $this->setConnection();
    }

    public function &setConnection() 
    {
        if (self::$pdo == NULL)
            self::$pdo = new \PDO('mysql:host=localhost;dbname=mobile_shop;charset=utf8', 'root', '', array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING));
        return self::$pdo;
    }
    public function &getConnection()
    {
        return self::$pdo;
    }
}

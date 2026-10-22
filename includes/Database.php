<?php

class Database
{
    private $connection;



    public function __construct($host, $dbname, $user, $pass)
    {
        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION ,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
        } catch (PDOException $e) {
            die('Database connection failed');
        }
    }


    public function getConnection(){
        return $this->connection;
    }
}

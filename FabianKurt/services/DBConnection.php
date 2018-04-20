<?php


namespace services;


class DBConnection
{

    private static $dbConnection;

    public static function getDbConnection(){
        if(DBConnection::$dbConnection){
            return DBConnection::$dbConnection;
        }else{
            DBConnection::createConnection();
            return DBConnection::$dbConnection;
        }
    }

    private static function createConnection(){
        DBConnection::$dbConnection = new \PDO('mysql:host=localhost;dbname=RProjekt','root','');
    }

}
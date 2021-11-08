<?php 

/** 
 * This class provides a connection to the database you selected  
 *
 * @author  Alireza Mohammadi
 * @version 1.0v
 */

class DatabaseConnection {
    private static $SERVER_NAME = "localhost";
    private static $USER_NAME = "root";
    private static $PASSWORD = "";
    private static $DATABASE_NAME = "signin-signup-system";
    private static $connection;



    /**
    * This method establish a connection to the database
    *
    * @return self::$connection
    */
    public static function getConnection() {
        try {
            self::$connection = new mysqli(self::$SERVER_NAME, self::$USER_NAME, self::$PASSWORD, self::$DATABASE_NAME);
            //echo "connected";
        } catch(mysqli_sql_exception $ex) {
            echo "Error: ". $ex->getMessage();
            die();
        }
        return self::$connection;
    }
}

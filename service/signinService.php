<?php

session_start();
require_once("../database/database.php");

/** 
 * This class get the inputs from the signin page and checks in diffrent methods if the inputs are correct.
 * If yes, it will login the user  
 *
 * @author  Alireza Mohammadi
 * @version 1.0v
 * @property string $email_address
 * @property string $username
 * @property string $password
 * @property string $userId
 */
class Signin {
    private $username;
    private $password;
    private $userId;

    /**
    * The constructer start the signin methods
    */
    public function __construct()
    {
        $this->getInputData();
    }

    /**
    * This method gets the inputs from the user and store it in the rigth variables
    * after stroting the data, it will reun the checkEmpty function
    */
    private function getInputData() {
        if(isset($_POST["signinButton"])) {
            $this->username= htmlspecialchars(filter_input(INPUT_POST, "username"));
            $this->password= htmlspecialchars(filter_input(INPUT_POST, "password"));
            $this->checkIfEmpty();
        }
    }

    /**
    * This method checks if the inputs are empty. If it is empty, it will redirect the user again to the signin page and shows the user which input filed was empty
    * The error message is stored in a SESSION variable so that we can show the error message
    */
    private function checkIfEmpty() {
        $val_username = trim($this->username);
        $val_password = trim($this->password);

        if(empty($val_username)) {
            $_SESSION["error_login"] = "Username can not be empty!";
            header("Location: ../signin.php");
            exit();
        }
        elseif(empty($val_password)) {
            $_SESSION["error_login"] = "Password can not be empty!";
            header("Location: ../signin.php");
            exit();
        }
        elseif(!empty($val_username) && !empty($val_password)) {
            $this->loginUser();
        }
    }

    /**
    * This method will run login the user
    */
    private function loginUser() {
        $this->getUserFromDatabase();
    }

    /**
    * This method gets the user from the database and check if it machtes with the user inputs if tru it logs the user in
    */
    private function getUserFromDatabase() {
        $userIdFromDB = "";
        $usernameFromDB = "";
        $passwordFromDB = "";

        try {
            $connection = DatabaseConnection::getConnection();

            $sql = "SELECT userId, username, userpassword FROM users where username = ?;";

            $statment = $connection->prepare($sql);
            $statment->bind_param("s", $this->username);
            $statment->execute();
            $statment->store_result();
            $connection->close();
        }catch (Exception $ex) {
            die($ex->getMessage());
        }

        if($statment->num_rows > 0) {
            $statment->bind_result($userIdFromDB, $usernameFromDB, $passwordFromDB);
            $statment->fetch();
            if((password_verify($this->password, $passwordFromDB)) && ($this->username === $usernameFromDB)) {
                session_regenerate_id();
                $_SESSION["loggedin"] = true;
                $_SESSION["userId"] = $this->userId;
                $_SESSION["username"] = $this->username;
                header("Location: ../home.php");
                exit();
            }
            else {
                $_SESSION["error_login"] = "Username or Password is not correct!";
                header("Location: ../signin.php");
                exit();
            }    
        }
    }
}


/**
* Create an instance of the Signin class
*/
$login = new Signin();
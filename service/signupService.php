<?php
session_start();

require_once("../database/database.php");

/** 
 * This class get the inputs from the signup page and checks in diffrent methods if the inputs are valid.
 * If yes, it will add the user to the database, so the user can login  
 *
 * @author  Alireza Mohammadi
 * @version 1.0v
 * @property string $email_address
 * @property string $username
 * @property string $password
 * @property boolean $validEmail
 * @property boolean $validUsername
 * @property boolean $validPassword
 * @property string $usernameFromDB
 * @property string $emailFromDB
 */
class Signup {
    private $email_address;
    private $username;
    private $password;
    private $validEmail = false;
    private $validUsername = false;
    private $validPassword = false;
    private $usernameFromDB;
    private $emailFromDB;

    /**
    * The constructer start the signup methods
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
        if(isset($_POST["signupButton"])) {
            $this->email_address = htmlspecialchars(filter_input(INPUT_POST, "email"), FILTER_SANITIZE_EMAIL);
            $this->username= htmlspecialchars(filter_input(INPUT_POST, "username"));
            $this->password= htmlspecialchars(filter_input(INPUT_POST, "password"));
            $this->checkIfEmpty();
        }
    }

    /**
    * This method checks if the inputs are empty. If it is empty, it will redirect the user again to the signup page and shows the user which input filed was empty
    * The error message is stored in a SESSION variable so that we can show the error message
    */
    private function checkIfEmpty() {
        $val_email = trim($this->email_address);
        $val_username = trim($this->username);
        $val_password = trim($this->password);

        if(empty($val_email)) {
            $_SESSION["error"] = "Email can not be empty";
            header("Location: ../signup.php");
            exit();
        }
        elseif(empty($val_username)) {
            $_SESSION["error"] = "Username can not be empty!";
            header("Location: ../signup.php");
            exit();
        }
        elseif(empty($val_password)) {
            $_SESSION["error"] = "Password can not be empty!";
            header("Location: ../signup.php");
            exit();
        }
        elseif(!empty($val_email) && !empty($val_username) && !empty($val_password)) { //if all of the input fields are not empty then run some methods
            $this->validateEmailAddress();
            $this->validateUsername();
            $this->validatePassword();
            if(!$this->checkIfUsernameExist()) {
                if(!$this->checkIfEmailAddressExist()) {
                    $this->createNewUser();
                }
                else {
                    $_SESSION["error"] = "Email address exist already!";
                    header("Location: ../signup.php");
                    exit();
                }
            }
            else {
                $_SESSION["error"] = "Username exist already!";
                header("Location: ../signup.php");
                exit();
            }
        }
    }

    /**
    * This method checks if the enterd email address is a valid email address, it will redirect the user to the signup page and shows a error message what the email address should contains
    * The error message is stored in a SESSION variable so that we can show the error message
    */
    private function validateEmailAddress() {
        $val = trim($this->email_address);

       if(!filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->validEmail = true; //there is an mistake
            $_SESSION["error"] = "Email Address not valid!";
            header("Location: ../signup.php");
            exit();
        }
        else {
            $this->validEmail = false;
        }
    }

    /**
    * This method checks if the enterd username is valid, it will redirect the user to the signup page and shows a error message what the username should contains
    * The error message is stored in a SESSION variable so that we can show the error message
    */
    private function validateUsername() {
        $val = trim($this->username);
        $usernameFromDB = $this->checkIfUsernameExist();

        if(!preg_match("/^(?=.*\d)(?=.*[A-Za-z])[a-zA-Z0-9]*$/", $val) && $usernameFromDB !== $this->username) {
            $_SESSION["error"] = "Username is to short or it exists already";
            $this->validUsername = true;
            header("Location: ../signup.php");
            exit();
        }


        else {
            $this->validUsername = false;
        }
    }

    /**
    * This method checks if the enterd username already exists in the database, it will redirect the user to the signup page and show a error message
    * The error message is stored in a SESSION variable so that we can show the error message
    */
    private function checkIfUsernameExist() {
        $resultCheck = false;

        try {
            $connection = DatabaseConnection::getConnection();

            $sql = "SELECT username FROM users where username = ?;";

            $statment = $connection->prepare($sql);
            $statment->bind_param("s", $this->username);
            $statment->execute();
            $statment->store_result();
            $connection->close();
        }catch (Exception $ex) {
            die($ex->getMessage());
        }

        if($statment->num_rows > 0) {
            $statment->bind_result($this->usernameFromDB);
            $statment->fetch();
            $resultCheck = true;
        }
        else {
            $resultCheck = false;
        }
        return $resultCheck;
    }

    /**
    * This method checks if the enterd email address already exists in the database, it will redirect the user to the signup page and show a error message
    * The error message is stored in a SESSION variable so that we can show the error message
    */
    private function checkIfEmailAddressExist() {
        $resultCheck = false;

        try {
            $connection = DatabaseConnection::getConnection();

            $sql = "SELECT userEmail FROM users where userEmail = ?;";

            $statment = $connection->prepare($sql);
            $statment->bind_param("s", $this->email_address);
            $statment->execute();
            $statment->store_result();
            $connection->close();
        }catch (Exception $ex) {
            die($ex->getMessage());
        }

        if($statment->num_rows > 0) {
            $statment->bind_result($this->emailFromDB);
            $statment->fetch();
            $resultCheck = true;
        }
        else {
            $resultCheck = false;
        }
        return $resultCheck;
    }

    /**
    * This method checks if the enterd password is valid, it will redirect the user to the signup page and shows a error message what the password should contains
    * The error message is stored in a SESSION variable so that we can show the error message
    */
    private function validatePassword() {
        $val = trim($this->password);

        /*
            Between start -> ^
            And end -> $
            of the string there has to be at least one number -> (?=.*\d)
            and at least one letter -> (?=.*[A-Za-z])
            and it has to be a number, a letter or one of the following: !@#$% -> [0-9A-Za-z!@#$%]
            and there have to be 8-12 characters -> {8,12}
        */
        if(!preg_match("/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{12,40}$/", $val)) {
            $_SESSION["error"] = "Try another password!";
            $this->validPassword = true;
            header("Location: ../signup.php");
            exit();
        }
        else {
            $this->validPassword = false;
        }
    }

    /**
    * If all inputs are correct, then this method will show a message and call another method which adds the user to the database 
    */
    private function createNewUser() {
        if(!$this->validEmail && !$this->validUsername && !$this->validPassword) {
            $_SESSION["error"] = "Thanks for Registration. Now you can login";
            $this->addUserToDatabase();
            header("Location: ../signup.php");
            exit();
        }
    }

    /**
    * This method adds the user to the database so the user can login
    */
    private function addUserToDatabase() {
       try {
            $connection = DatabaseConnection::getConnection();  //get the connection from database class

            $userId = $this->createUserId();
            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT, ["cost"=>15]);

            $sql = "INSERT INTO users (userId, username, userpassword, userEmail) VALUES(?, ?, ?, ?)";

            $statment = $connection->prepare($sql);
            $statment->bind_param("ssss", $userId, $this->username, $hashedPassword, $this->email_address);
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        if($statment->execute()) {
            $this->userCreated = true;
        }
        else {
            $this->userCreated = false;
        }
    }


    /**
    * This method creates a uniq user id to identify the user
    * @return int $uid
    */
    private function createUserId() {
        $uid = uniqid();
        return $uid;
    }
}

/**
* Create an instance of the Signup class
*/
$re = new Signup();
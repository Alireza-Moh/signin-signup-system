<?php

session_start();

/**
 * @author  Alireza Mohmmadi
 * it destroys the SESSION after the user clicked the logout button
 */
if(session_id()) {
    session_unset();
    session_destroy();
    header("Location: ../signin.php");
    exit();
}
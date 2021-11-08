<?php
    session_start();
    
    if(!isset($_SESSION["loggedin"])) {
        header("Location: ./signin.php");
        exit();
    }

    require_once("./include/header.php");
?>
<!--home container-->
<div class="main-container-home">
    <!--banner container-->
    <div class="banner-container">
        <div class="inner-banner-container">
            <p class="headline">Now youcan logout...</p>
            <li type="submit" name="logoutButtonHome" class="logoutButtonHome"><a href="./service/logoutService.php" class="logout-link">Logout</a></li>
        </div>
    </div>
    <!--banner container end-->
</div>
<!--home container end-->
<?php
    require_once("./include/footer.php");
?>
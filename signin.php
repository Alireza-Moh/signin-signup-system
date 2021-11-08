<?php
    session_start();

    require_once("./include/header.php");
?>
<!--signin form-->
<div class="form-container-signin-page">
    <div class="invalidInputField">
        <?php
            if(isset($_SESSION["error_login"])) {
                echo $_SESSION["error_login"];
                unset($_SESSION["error_login"]);?>
                <script>
                    const invalidInputField = document.querySelector(".invalidInputField");
                    invalidInputField.style.display = "block";
                </script>
        <?php
            } 
            else { ?>
                <script>
                    invalidInputField.style.display = "none";
                </script>
        <?php
            }
        ?>
    </div>
    <form action="./service/signinService.php" class="signin-form-container" method="POST">
        <div class="headline">Login</div>
        <input type="text" class="inputs" name="username" placeholder="Username">
        <input type="password" class="inputs" name="password" placeholder="Password">
        <a href="#" class="forgot-password">Forgot Password?</a>
        <button type="submit" name="signinButton" class="signinButton">Login</button>
        <a href="./signup.php" class="login-link">Signup</a>
    </form>
</div>
<!--signin form end-->
<?php
    require_once("./include/footer.php");
?>
<?php
    session_start();

    require_once("./include/header.php");
?>

<!--signup form-->
<div class="form-container-signup-page">
    <div class="invalidInputFieldSignup">
            <?php
                if(isset($_SESSION["error"])) {
                    if($_SESSION["error"] === "Thanks for Registration. Now you can login") {
                        echo $_SESSION["error"];
                        unset($_SESSION["error"]); ?>
                        <script>
                            const invalidInputField = document.querySelector(".invalidInputFieldSignup");
                            invalidInputField.style.backgroundColor = "#d4edda";
                            invalidInputField.style.color = "#65927c";
                            invalidInputField.style.display = "block";
                            function destroyInavlidInputField() {
                                console.log("destroyed");
                                setTimeout(function() {
                                    invalidInputField.remove();
                                    window.location.replace("./signin.php");
                                }, 2000);
                            }
                            destroyInavlidInputField();
                        </script>
            <?php   }
                    elseif($_SESSION["error"] !== "Thanks for Registration. Now you can login") { 
                            echo $_SESSION["error"];
                            unset($_SESSION["error"]); ?>
                            <script>
                                const invalidInputField = document.querySelector(".invalidInputFieldSignup");
                                invalidInputField.style.display = "block";
                                invalidInputField.style.width = "250px";
                            </script>
                <?php   }
                        else { ?>
                            <script>
                                const invalidInputField = document.querySelector(".invalidInputFieldSignup");
                                invalidInputField.style.display = "none";
                            </script>
                <?php   } ?>
        <?php   }
                else { ?>
                    <script>
                        const invalidInputField = document.querySelector(".invalidInputFieldSignup");
                        invalidInputField.style.display = "none";
                    </script>
        <?php   } ?>
        </div>
    <form action="./service/signupService.php" class="signup-form-container" method="POST">
        <div class="headline">Register</div>
        <input type="text" class="inputs" name="email" placeholder="E-Mail">
        <input type="text" class="inputs" name="username" placeholder="Username">
        <input type="password" class="inputs" name="password" placeholder="Password">
        <button type="submit" name="signupButton" class="signupButton">Register</button>
        <a href="./signin.php" class="login-link">Login</a>
    </form>
</div>
<!--signup form end-->
<?php
    require_once("./include/footer.php");
?>
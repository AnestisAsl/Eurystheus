<?php require_once 'controllers/authController.php';?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width,initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" >
    <link rel="stylesheet" href="cssFiles/style.css">
    <link rel="stylesheet" href="cssFiles/buttonstyle.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="images/eu1.jpg">

    <title>Forgot Password</title>
</head>
<body>
    <div class="flex-container" style="align-items:center;justify-content: center;">
        <div class="preload">
            <h3>Loading</h3>
            <div class="loader">
                <div class="circle"></div>
                <div class="circle"></div>
            </div>
        </div>
        <form action ="forgotPassword.php" method="post">
            <h1 class="titleDisplay" id="titleDisplayForForgotPasswordPHP">Recover your password</h1>
            <?php if(count($errors)>0): ?>
                <div class="alert alert-danger">
                    <?php foreach($errors as $error):?>
                        <li><?php echo $error;?></li>
                    <?php endforeach;?>
                </div>
            <?php endif;?>
                <p class="message">Please enter the email address you used for signing-Up<br>
                We will assist you recovering your password.
                </p>
                
                <div class="formParent">
                    <div class="formChild">
                        <li class="icons"><i class="fas fa-envelope-square"></i></li>
                    </div>
                    <div class="username">
                        <input type="text" name="email" value="<?php if(isset($_SESSION['email'])){ echo $_SESSION['email'];}?>"autocomplete="off" required >
                        <label for="email" class="usernameLabel">
                            <span class="contentUsername">Email</span>
                        </label>
                    </div> 
                </div> 
                <div>
                    <button type="submit" name="forgot-password-btn" class="btn loginButton">Recover password</button>
                </div>       
        </form>
        <form action="index.php">
            <button type="submit" name="back" class="btn loginButton">Back</button>
        </form>
    </div> 
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
window.addEventListener('load',() =>{
    const preload=document.querySelector('.preload');
    preload.classList.add('preload-finish');
});

</script>
</html>
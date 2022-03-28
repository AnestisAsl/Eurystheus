<?php 
require_once 'controllers/authController.php';
require_once 'config/constants.php'              
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" >
    <link rel="stylesheet" href="cssFiles/style.css">
    <link rel="stylesheet" href="cssFiles/styleMenu.css">
    <link rel="stylesheet" href="cssFiles/buttonstyle.css">
    <link rel="shortcut icon" type="image/png" href="images/eu1.jpg">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <title>Login</title>
</head>
<body>
    
    <div class="flex-container"  style="align-items: center;justify-content: center;">
        <div class="preload">
            <h3>Loading</h3>
            <div class="loader">
                <div class="circle"></div>
                <div class="circle"></div>
            </div>
        </div>
        <div id="styleDiv">
            Everything about dissertations<button id="closeButtonId"><i class="far fa-times-circle"></i></button>
            <div id=flip>
                <div><div>REQUESTS</div></div>
                <div><div>THEMES</div></div>
                <div><div>AND MORE</div>
            </div>
            </div>
            With ease by <span>Eurysteus</span> 
        </div>

        <div class="flex-container"  style="width:unset!important;height:unset!important;">
            <form action ="login.php" method="post">
                <?php if(count($errors)>0): ?>
                    <input type="text" id="ifErrorId" value=<?php echo  count($errors)?> style="display:none;">
                    <div class="alert alert-danger">
                        <?php foreach($errors as $error):?>
                            <li><?php echo $error;?></li>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>


                <h1 class="titleDisplay" id=loginTitleId>Login</h1>
                    

                <div class="formParent">
                    <div class="formChild">
                    <li class="icons"><i class="fas fa-users"></i></li>
                    </div>
                    <div class="username">
                        <input type="text" name="username" value="<?php echo $username;?>" autocomplete="off" required>
                        <label for="username" class="usernameLabel"> 
                            <span class="contentUsername">Username or Email</span>
                        </label>
                    </div>
                </div>
                <div class="formParent">
                    <div class="formChild">
                        <li class="icons"><i class="fas fa-unlock"></i></li>
                    </div>
                    <div class="username">
                        <input type="password" name="password" autocomplete="off" required>
                        <label for="username" class="usernameLabel">
                            <span class="contentUsername">Password</span>
                        </label>
                    </div>  
                </div>
                <div style="float:right;">
                    <button type="submit" name="login-btn" class="btn loginButton">Login</button>
                </div>
            </form>
        </div>
        <div style="width:30%;">
            <form action ="forgotPassword.php" method="post">
                <p class="message">Forgot your password?</p>
                <button type="submit" name="forgotYourPasswordButton" class="btn loginButton" id="ForgotMyPasswordId">Forgot my password</button>
            </form>
        </div>
        <div style="width:30%;">
            <form action ="signup.php" method="post">
                <p class="message">Not yet a member?</p>
                <button type="submit" name="signupButton" class="btn signupButton">Sign Up</button>
            </form>
        </div>
        <div class="toInfoPage" style="position:absolute">
            <form action="infopage.php">
                <h3 id="backToInfoPageTitle">Back to Welcome Page</h3>
                <button type="submit" name="toInfoPage" class="toInfoPageBtn"></button>
            </form>
        </div>

    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    window.addEventListener('load',() =>{
        const preload=document.querySelector('.preload');
        preload.classList.add('preload-finish');
    });
    var closeButton=document.getElementById('closeButtonId');
    var styleDiv=document.getElementById('styleDiv');
    closeButton.onclick = function(){
        styleDiv.style.display = "none";
    };
    var ifErrorId=$('#ifErrorId');
    console.log(ifErrorId.val());
    if ((ifErrorId.val()!==undefined)) {
        styleDiv.style.display = "none";
    }
    if($(window).width()>900){
        $(".toInfoPageBtn").css("width",100);
        $(".toInfoPageBtn").css("height",100);

        $(".toInfoPage").css("right",$(window).width()/2+500);
        $(".toInfoPage").css("top",$(window).height()-200);

    }else{
        $(".toInfoPage").hide();
    }
    
    $(".toInfoPageBtn").hover(
        function () {
            $("#backToInfoPageTitle").addClass('hoverTitle');
        }, 
        function () {
            $("#backToInfoPageTitle").removeClass('hoverTitle');
        }
    );
</script>
</html>
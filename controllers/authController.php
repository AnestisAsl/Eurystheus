<?php

require 'config/db.php';
require_once 'emailController.php';
require_once 'config/constants.php';

session_start();

$AM="";
$errors=array();
$popUpMessage=array();

$username="";
$email="";
$password="";
//from constants.php
$secretKey=secretKey;

//collect the informations of the fields by clicking signup-btn button
if(isset($_POST['signup-btn'])){

    //recaptcha
    recaptchaValidation();

    $username=$_POST['username'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $passwordConf=$_POST['passwordConf'];
    $AM=$_POST['AM'];
    $name=$_POST['name'];

    //validation
    if(empty($username)){
        $errors['username']="Username required";
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['email']="Email address is not valid";
    }
    if(empty($email)){
        $errors['email']="Email required";
    }
    if(empty($password)){
        $errors['password']="Password required";
    }
    if(empty($AM)){
        $errors['AM']="A.M. required";
    }
    if($password != $passwordConf){
        $errors['password']="The two passwords do not match";
    }
    if(empty($name)){
        $errors['name']="Name required";
    }

    //duplicate email not allowed (same email for multiple users)
    $emailQuery="SELECT * FROM users WHERE email=? LIMIT 1";
    $stmt=$conn->prepare($emailQuery);
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $result=$stmt->get_result();
    $userCount=$result->num_rows;
    $stmt->close();
    if($userCount>0){
        $errors['email']="Email already exists";
    }
    //duplicate A.M. not allowed (same A.M. for multiple users)
    $emailQuery="SELECT * FROM users WHERE AM=? LIMIT 1";
    $stmt=$conn->prepare($emailQuery);
    $stmt->bind_param('i',$AM);
    $stmt->execute();
    $result=$stmt->get_result();
    $userCount=$result->num_rows;
    $stmt->close();
    if($userCount>0){
        $errors['AM']="A.M. already exists";
    }
    //AM should be valid.I don't have access to all available AM so
    //i check if the AM is numeric
    if(!(is_numeric($AM))){
        $errors['AM']="A.M. should be numeric";
    }

    //In case a student set as username one of the default professor usernames
    //before a professor change it from his options.
    $ifStudentSetProfessorUsrname="SELECT * FROM profusers WHERE username=? LIMIT 1";
    $stmt=$conn->prepare($ifStudentSetProfessorUsrname);
    $stmt->bind_param('s',$username);
    $stmt->execute();
    $resultIfStudentSetProfessorUsrname=$stmt->get_result();
    $resCount=$resultIfStudentSetProfessorUsrname->num_rows;
    if($resCount>0){
        $stmt->close();
        $errors['username']="Invalid username";
    }

    if(count($errors)==0){
        //encrypt password (database show encrypted password)
        $password=password_hash($password,PASSWORD_DEFAULT);
        $token=bin2hex(random_bytes(50));
        $verified=false;
        $sql="INSERT INTO users(username,email,AM,verified,token,password,student_name) VALUES (?,?,?,?,?,?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('ssibsss',$username,$email,$AM,$verified,$token,$password,$name);
        if($stmt->execute()){
            //login user
            $user_id=$conn->insert_id;
            $_SESSION['id']=$user_id;
            $_SESSION['username']=$username;
            $_SESSION['email']=$email;
            $_SESSION['AM']=$AM;
            $_SESSION['verified']=$verified;
            $_SESSION['name']=$name;
            //token unique has been generated up
            sendVerificationEmail($email,$token);
            //$_SESSION['message']="You are now logged in";
            $_SESSION['alert-class']="alert-success";
            $stmt->close();
            header('location:index.php');
            exit();
        }else{
            $errors['db_error']="Database error : failed to register";
        }
    }
}

//for user login
if(isset($_POST['login-btn'])){
        
    $username=$_POST['username'];
    $password=$_POST['password'];
    
    if(isset($_SESSION['id'])||isset($_SESSION['profid'])){
        $errors['sessionManagement']="Another user is  logged in right now from the same<br> browser/pc.
        Cannot allow another<br> 'log in' action right now for security reasons.";
    }
    
    //validation
    if(empty($username)){
        $errors['username']="Username required";
    }

    if(empty($password)){
        $errors['password']="Password required";
    }
    
    if(count($errors)===0){
        $sql="SELECT *FROM users WHERE email=? OR username=? LIMIT 1";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('ss',$username,$username);
        $stmt->execute();
        $result=$stmt->get_result();
        $user=$result->fetch_assoc();
        $stmt->close();
        $sql2="SELECT *FROM profusers WHERE username=? LIMIT 1";
        $stmt2=$conn->prepare($sql2);
        $stmt2->bind_param('s',$username);
        $stmt2->execute();
        $result2=$stmt2->get_result();
        $prof=$result2->fetch_assoc();
        $stmt2->close();
        
        

        if(isset($user['username'])){
            //student exists
            //testing
            $file = fopen("tests/unit/user.txt", "w") or die("Unable to open file!");
            $txt =$user['username']."\n";
            fwrite($file, $txt);
            $txt =$user['student_name']."\n";
            fwrite($file, $txt);
            $txt =$user['AM']."\n";
            fwrite($file, $txt);
            fclose($file);
            
            if(count($errors)===0){
                if(password_verify($password,$user['password'])){
                    //login
                    $_SESSION['id']=$user['id'];
                    $_SESSION['username']=$user['username'];
                    $_SESSION['email']=$user['email'];
                    $_SESSION['AM']=$user['AM'];
                    $_SESSION['name']=$user['student_name'];
                    $_SESSION['verified']=$user['verified'];
                    //$_SESSION['message']="You are now logged in";
                    $_SESSION['alert-class']="alert-success";
                    header('location:index.php');
                    exit();
                }else{
                    $errors['login_fail']="Wrong credentials";  
                }
            }
        }else{
            if(isset($prof['username'])){
                //professor exists
                
                //testing
                $file = fopen("tests/unit/profUser.txt", "w") or die("Unable to open file!");
                $txt =$prof['username']."\n";
                fwrite($file, $txt);
                $txt =$prof['id']."\n";
                fwrite($file, $txt);
                fclose($file);

            
                if(count($errors)===0){

                    if($password==$prof['password'] || password_verify($password,$prof['password'])){
                        //login
                        $_SESSION['profid']=$prof['id'];
                        $_SESSION['profusername']=$prof['username'];
                        $_SESSION['profemail']=$prof['email'];
                        $_SESSION['profmessage']="You are now logged in";
                        $_SESSION['profalert-class']="alert-success";
                        header('location:professorHome.php');
                        exit();
                    }else{
                        $errors['login_fail']="Wrong credentials";
                    }
                }
            }else{
                $errors['username']="Username not found";
            }
        }
        
    }
}

//logout
if(isset($_POST['logoutButton'])){

    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['AM']);
    unset($_SESSION['verified']);
    if(!isset($_SESSION['profid'])){
        session_destroy();
        header('location:login.php');
        exit();      
    }

}

if(isset($_POST['logoutButtonProf'])){
    unset($_SESSION['profid']);
    unset($_SESSION['profusername']);
    unset($_SESSION['profemail']);
    unset($_SESSION['AM']);
    unset($_SESSION['verified']);
    if(!isset($_SESSION['id'])){
        session_destroy();
        header('location:login.php');
        exit();     
    }
}


//verify user by token
function verifyUser($token){

    global $conn;
    $sql="SELECT * FROM users WHERE token='$token' LIMIT 1";
    $result=mysqli_query($conn,$sql);

    if(mysqli_num_rows($result)>0){
        //user exists so i verify him
        $user=mysqli_fetch_assoc($result);
        $update_query ="UPDATE users SET verified=1 WHERE token='$token' ";

        if(mysqli_query($conn,$update_query)){
            $_SESSION['id']=$user['id'];
            $_SESSION['username']=$user['username'];
            $_SESSION['email']=$user['email'];
            $_SESSION['AM']=$user['AM'];
            $_SESSION['verified']=1;
            $_SESSION['message']="Your  email address was successfully verified";
            header('location:index.php');
            exit();
        }
    }else{
        //delete the account before verification(rare condition)
        $errors['username']="Username not found";
    }
}

//Reset password
if(isset($_POST['forgot-password-btn'])){

    $email=$_POST['email'];

    //validation for email (no@ or.com e.t.c)
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['email']="Email address is not valid";
    }
    if(empty($email)){
        $errors['email']="Email required";
    }
    if(count($errors)==0){
        $email =$conn->real_escape_string ($email);

        $sql="SELECT * FROM users WHERE email='$email' LIMIT 1";
        $result=mysqli_query($conn,$sql);
        $user=mysqli_fetch_assoc($result);
        $token=$user['token'];
        sendPasswordResetLink($email,$token);
        header('location:resetPasswordMessage.php');
        exit();
    }
}

//if user clicks on the reset password button
if(isset($_POST['resetPassword-btn'])){
    global $conn;
    
    $password=$_POST['password'];
    $passwordConf=$_POST['passwordConf'];
    //validation
    if(empty($password)||empty($passwordConf)){
        $errors['password']="Password required";
    }

    if($password !== $passwordConf){
        $errors['password']="The two passwords do not match";
    }

    $password=password_hash($password,PASSWORD_DEFAULT);
    $email=$_SESSION['email'];
    if(count($errors)==0){
        $email =$conn->real_escape_string ($email);


        $sql="UPDATE users SET password='$password' WHERE email='$email'";
        $result=mysqli_query($conn,$sql);
        if($result){
            header('location:login.php');
            exit;
        }
    }
}
//for user to see the current dissertation themes
if(isset($_POST['seeDissertation'])){
    header('location:dissertationDisplay.php');
}
//for user to give us feedback
if(isset($_POST['giveUsFeedback'])){
    header('location:feedback.php');
}

if(isset($_POST['signInButton'])){
    header('location:login.php');
}

if(isset($_POST['redirectLogin'])){
    header('location:login.php');
}

if(isset($_POST['redirectSignUp'])){
    header('location:signup.php');
}

if(isset($_POST['OKmessage'])){
    unset($_SESSION['message']);
}

//reseting password
//token is unique so if we found the token we found the user that wants to reset his password
function resetPassword($token){
    global $conn;
    $token =$conn->real_escape_string ($token);

    $sql="SELECT * FROM users WHERE token='$token' LIMIT 1";
    $result=mysqli_query($conn,$sql);
    $user=mysqli_fetch_assoc($result);
    //new session so i need to take the email again to make sure that there is still 
    //a user at the website (maybe account could have been deleted)
    $_SESSION['email']=$user['email'];
    header('location: resetPasswordForm.php');
    exit();
}

function recaptchaValidation(){
    
    global $errors;
    global $secretKey;
    $responseKey=$_POST['g-recaptcha-response'];
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey";
    $response=file_get_contents($url);
    $response=json_decode($response);
    if($response->success){
        //echo "correct recaptcha action";
    }else{
        $errors['recaptcha']="Check in the box";
    }
}
//has access to syle.css cause require_once at the top of the file (extra).
/*function errorPrinter(){
    global $errors;
    if(count($errors)>0): 
        foreach($errors as $error):
            echo "<form method='post'>";
                echo "<div class='errorStyle'>";
                    echo "<li>".$error."</li>";
                    echo "<button type='submit' name='errorBtn' class='btn loginButton'>OK</button>";
                echo "</div>";
            echo "</form>";
        endforeach;    
    endif;
}

*/

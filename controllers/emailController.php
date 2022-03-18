<?php 

//vendor libraries
require_once 'vendor/autoload.php';
require_once 'config/constants.php';

// Create the Transport
$transport = (new Swift_SmtpTransport('smtp.gmail.com', 465,'ssl'))//gmail server port 465 standard
  ->setUsername(EMAIL)//the constants.php values
  ->setPassword(PASSWORD);


// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);



function sendVerificationEmail($userEmail,$token){
    
  
  global $mailer;
  $body='<!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      
      <title>Verify email</title>
  </head>
  <body>
     <div class="wrapper">
         <p>
             click link to Verify your email.
         </p>
         <a href="http://localhost/project/index.php?token=' .$token .'"> 
            Verify your email address
         
         </a>
     </div> 
  </body>
  </html>';
    //php library ths php 

    // Create a message
    $message = (new Swift_Message('Verify your email address'))
    ->setFrom(EMAIL)
    ->setTo($userEmail)
    ->setBody($body,'text/html');
    

    // Send the message
    $result = $mailer->send($message);

}

//the fuction for resetting passwords
function sendPasswordResetLink($userEmail,$token){
    global $mailer;
  $body='<!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      
      <title>Verify email</title>
  </head>
  <body>
     <div class="wrapper">
         <p>
             click the link to reset your password
         </p>
         <a href="http://localhost/project/index.php?password-token=' .$token .'"> 
            Reset your password
         
         </a>
     </div> 
  </body>
  </html>';
    //php library
    // Create a message
    $message = (new Swift_Message('Reset your password'))
    ->setFrom(EMAIL)
    ->setTo($userEmail)
    ->setBody($body,'text/html');
    

    //Send the message
    $result = $mailer->send($message);
}


function requestNotification($userEmail){
  global $mailer;
  $body='<!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      
      <title>Request</title>
  </head>
  <body>
     <div class="wrapper">
         <p>
             A student has sent you a request.Login by clicking the link below.
             <a href="http://localhost/project/login.php"> 

         </p>
     </div> 
  </body>
  </html>';
    //php library
    // Create a message
    $message = (new Swift_Message('Request from student'))
    ->setFrom(EMAIL)
    ->setTo($userEmail)
    ->setBody($body,'text/html');
    //Send the message
    $result = $mailer->send($message);
}

function acceptNotification($userEmail){
    global $mailer;
    $body='<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        
        <title>Accepted</title>
    </head>
    <body>
       <div class="wrapper">
           <p>
               A professor accepted your request.Login by clicking the link below.
               <a href="http://localhost/project/login.php"> 
  
           </p>
       </div> 
    </body>
    </html>';
      //php library
      // Create a message
      $message = (new Swift_Message('Accepted by a professor'))
      ->setFrom(EMAIL)
      ->setTo($userEmail)
      ->setBody($body,'text/html');
      //Send the message
      $result = $mailer->send($message);
  }

function changeProfLogData($userEmail,$token){
    global $mailer;
    $body='<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        
        <title>Verify email</title>
    </head>
    <body>
       <div class="wrapper">
           <p>
             click the link to reset your password
         </p>
         <a href="http://localhost/project/professorHome.php?password-token=' .$token .'"> 
            Reset your password
         
         </a>
       </div> 
    </body>
    </html>';
      //php library
      // Create a message
      $message = (new Swift_Message('Change Login Data'))
      ->setFrom(EMAIL)
      ->setTo($userEmail)
      ->setBody($body,'text/html');
      //Send the message
      $result = $mailer->send($message);
  }
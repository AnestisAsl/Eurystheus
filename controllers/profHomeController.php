<?php 

require_once 'config/constants.php';
require_once 'controllers/authController.php';

$firstname="";
$lastname="";

$sql="SELECT *FROM profusers WHERE username=? LIMIT 1";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('s',$_SESSION['profusername']);
    $stmt->execute();
    $result=$stmt->get_result();
    $professor=$result->fetch_assoc();
    $stmt->close();
    $prof_id=$professor['id'];
    $requirements="";
    $isRelated="";
    $databaseKeyword ="";
    if(isset($_POST['publishDissertation'])){
        //requirements
        if(!empty($_POST["reqName"])){
            foreach($_POST["reqName"] as $name)
            {
                $requirements .= $name . ', ';
            }
        }
        $requirements = substr($requirements, 0, -2);
        //If the theme is related with Professor's Courses
        if(isset($_POST['yesNo'])){
            $isRelated=$_POST['yesNo'];
        }
        //check if date is valid
        $currentTimeinSeconds = time();
        $currentDate = date('Y-m-d', $currentTimeinSeconds); 
        if(($currentTimeinSeconds>=$startDate and $currentTimeinSeconds<=$endDate)){
             //get keywords
            $databaseKeyword = '';
            
            if(!empty($_POST["keyword"])){
                foreach($_POST["keyword"] as $keyword)
                {
                    $databaseKeyword .= $keyword . ', ';
                }
            }
            $databaseKeyword = substr($databaseKeyword, 0, -2);
            //charts
            if(!empty($_POST["keyword"])){
                foreach($_POST["keyword"] as $keyword){
                    $sql="SELECT *FROM statistics WHERE keyword=? LIMIT 1";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param('s',$keyword);
                    if($stmt->execute()){
                        $resultKey=$stmt->get_result();
                        $statistics=$resultKey->fetch_assoc();
                        $stmt->close();
                    }else{
                        $errors['select']="DB error,Select has falied";
                    }
                    if(isset($statistics['keyword'])){
                        $sql="UPDATE statistics SET number_of_keyword_published=number_of_keyword_published+1 WHERE keyword=?";
                        $stmt=$conn->prepare($sql);
                        $stmt->bind_param('s',$keyword);
                        if($stmt->execute()){
                            $stmt->close();
                        }else{
                            $errors['update']="DB error,Update has falied";
                        }
                    }else{
                        $sql="INSERT INTO statistics(keyword,number_of_keyword_published) VALUES (?,?)";
                        $numberOfKeywordPublished=1;
                        $stmt=$conn->prepare($sql);
                        $stmt->bind_param('si',$keyword,$numberOfKeywordPublished);
                        if($stmt->execute()){
                            $stmt->close();
                        }else{
                            $errors['insert']="DB error,Insert has falied";
                        }
                    }    
                }
            }   
            $theme=$_POST['theme'];
            $description=$_POST['description'];

            $sql="INSERT INTO dissertation(prof_id,theme,description,keyword,requirements,isRelatedToMyCourses) VALUES (?,?,?,?,?,?)";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param('isssss',$prof_id,$theme,$description,$databaseKeyword,$requirements,$isRelated);
            /*published themes automatically saved (EXTRA)
            $sql2="INSERT INTO saveddissertation(prof_id,saved_theme,saved_description) VALUES (?,?,?)";
            $stmt2=$conn->prepare($sql2);
            $stmt2->bind_param('iss',$prof_id,$theme,$description);
            $stmt2->execute();
            $stmt2->close();*/

            if($stmt->execute()){
                $stmt->close();

                //testing
                $file = fopen("tests/unit/publishDissertation.txt", "w") or die("Unable to open file!");
                $txt =$prof_id."\n";
                fwrite($file, $txt);
                $txt =$theme."\n";
                fwrite($file, $txt);
                $txt =$databaseKeyword."\n";
                fwrite($file, $txt);
                $txt =$requirements."\n";
                fwrite($file, $txt);
                $txt =$isRelated."\n";
                fwrite($file, $txt);
                fclose($file);

                $_SESSION['message']="Dissertation published successfully";
                header("location: professorHome.php");
            }else{
                $errors['insert']="DB error,Insert has failed";
            }
              
        }else{
            $errors['time']="Date error ,you can't publish a dissertation now";
        }
    }
    //save theme and description without publishing it
    if(isset($_POST['saveButton'])){
        $saved_description=$_POST['description'];
        $saved_theme=$_POST['theme'];
        $sql="INSERT INTO saveddissertation(prof_id,saved_theme,saved_description) VALUES (?,?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('iss',$prof_id,$saved_theme,$saved_description);
        if($stmt->execute()){
            $stmt->close();
            $_SESSION['message']="Dissertation saved successfully";

            //testing
            $file = fopen("tests/unit/saveDissertationProf.txt", "w") or die("Unable to open file!");
            $txt =$prof_id."\n";
            fwrite($file, $txt);
            $txt =$saved_theme."\n";
            fwrite($file, $txt);
            fclose($file);

            header("location: professorHome.php");  
        }else{
            $errors['insert']="DB error,Insert has failed";
        }
}
 

$sql="SELECT prof_name FROM profdata WHERE prof_id=? LIMIT 1";
$stmt=$conn->prepare($sql);
$stmt->bind_param('i',$prof_id);
$stmt->execute();
$result=$stmt->get_result();
$professor=$result->fetch_assoc();
$stmt->close();
if(isset($_POST['submit'])){
    $firstname=$_POST['firstname'];
    $lastname=$_POST['lastname'];
    $prof_name=$firstname."  ".$lastname;
    if(isset($professor['prof_name'])){
        $sql="UPDATE profdata SET prof_name=? WHERE prof_id=?";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('si',$prof_name,$prof_id);
        if($stmt->execute()){
            //success
        }else{
            $errors['update']="DB error,Update has failed";
        }
        $resultUpdate=$stmt->get_result();
        $stmt->close();
    }else{  
        $sql="INSERT INTO profdata(prof_id,prof_name) VALUES (?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('is',$prof_id,$prof_name,);
        if($stmt->execute()){
            $stmt->close();
            $_SESSION['message']="Data updated successfully";
            header("location: professorHome.php");
        }  
    }
}
//add keyword to a specific professor
if(isset($_POST['addKeyword'])){
    //check if he has already added some keywords
    $sql1="SELECT prof_keywords FROM profdata WHERE prof_id=? LIMIT 1";
    $stmt1=$conn->prepare($sql1);
    $stmt1->bind_param('i',$_SESSION['profid']);
    $stmt1->execute();
    $result=$stmt1->get_result();
    $professor=$result->fetch_assoc();
    if(isset($professor['prof_keywords'])){
        $keyword=$_POST['otherKeyword'].",".$professor['prof_keywords'];
    }else{
        $keyword=$_POST['otherKeyword'];
    }
    $sql="UPDATE profdata SET prof_keywords=? WHERE prof_id=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('si',$keyword,$_SESSION['profid']);
    if($stmt->execute()){
        $stmt->close() ;
        $stmt1->close() ;
        $_SESSION['message']="Field added successfully";

        //testing
        $file = fopen("tests/unit/addCSField.txt", "w") or die("Unable to open file!");
        $txt =$_SESSION['profid']."\n";
        fwrite($file, $txt);
        $txt =$keyword."\n";
        fwrite($file, $txt);
        fclose($file);

        header("location: professorHome.php");  
    }else{
        $errors['update']="DB error ,Update has failed";
    }  
}

//remove personal keyword
if(isset($_POST['removeKeyword'])){
    //update the table at the database 'prof_keywords FROM profdata'
    //with the same string except the one that professor wants to delete
    $keyword=$_POST['otherKeyword'];
    $sql="SELECT prof_keywords FROM profdata WHERE prof_id=? LIMIT 1";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('i',$_SESSION['profid']);
    $stmt->execute();
    $result=$stmt->get_result();
    $professor=$result->fetch_assoc();
    $spKeyword=explode(",",$professor['prof_keywords']);
    $newKeywords='';
    
    foreach ($spKeyword as $str){ 
        if($str!==$keyword){
            $newKeywords.=$str.",";
        }
    }
    
    $newKeywords = substr($newKeywords, 0, -1);
    $sql1="UPDATE profdata SET prof_keywords=? WHERE prof_id=?";
    $stmt1=$conn->prepare($sql1);
    $stmt1->bind_param('si',$newKeywords,$_SESSION['profid']);
    if($stmt1->execute()){
        $stmt->close() ;
        $stmt1->close() ;
        $_SESSION['message']="CS field removed successfully";
        header("location: professorHome.php");
    }else{
        $errors['update']="DB error ,Update has failed";
    }  
}


//just to make the values of first and last name visible even if
//professor clicks on 'close' button
if(isset($_POST['close'])){
    $firstname=$_POST['firstname'];
    $lastname=$_POST['lastname'];
    $prof_name=$firstname."  ".$lastname;

}
//delete a saved theme
if(isset($_POST['delete'])){
    if(isset($_POST['themeFromListProf'])){
        $saved_theme=$_POST['themeFromListProf'];
        $sql1="DELETE from saveddissertation WHERE prof_id=? and saved_theme=? LIMIT 1";
        $stmt1=$conn->prepare($sql1);
        $stmt1->bind_param('is',$_SESSION['profid'],$saved_theme);
        if($stmt1->execute()){
            $stmt1->close() ;
            $_SESSION['message']="Theme deleted successfully";
            header("location: professorHome.php");
        }else{
            $errors['delete']="DB error ,Delete has failed";
        }
    }
}
//delete published Theme
if(isset($_POST['deletePublishedTheme'])){
    if(isset($_POST['publishedThemeLoaded'])){
        $published_theme=$_POST['publishedThemeLoaded'];
        echo $published_theme;
        $sql="DELETE from dissertation WHERE prof_id=? and theme=? LIMIT 1";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('is',$_SESSION['profid'],$published_theme);
        if($stmt->execute()){
            $stmt->close() ;
            $_SESSION['message']="Theme deleted successfully";
            //header("location: professorHome.php");
        }else{
            $errors['delete']="DB error ,Delete has failed";
        }
    }
}


//request form 
if(isset($_POST['loadRequest'])){
    $_SESSION['requestTheme']=$_POST['request'];
    $_SESSION['requestAM']=$_POST['requestAM'];
    $sql="SELECT *FROM request where saved_theme=? and AM=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('si',$_SESSION['requestTheme'],$_SESSION['requestAM']);
    if($stmt->execute()){
        $resultReq=$stmt->get_result();
        $resRequest=$resultReq->fetch_assoc();
        $_SESSION['requestDescription']=$resRequest['saved_description'];
        $_SESSION['requestInfos']=$resRequest['informations'];
        $_SESSION['requestEmail']=$resRequest['communicationemail'];
        $_SESSION['requestStudentName']=$resRequest['student_name'];
        $_SESSION['studentIdForUploads']=$resRequest['id'];
        $stmt->close() ;
        header("location: showLoadedRequest.php");
    }else{
        $errors['select']="DB error ,Select has failed";
    }
    
}


//accept request
if(isset($_POST['acceptRequest'])){
    
    $prof_id=$_SESSION['profid'];
    $theme=$_POST['themeReq'];
    $description=$_POST['descriptionReq'];
    $AM=$_POST['AM'];
    $email=$_POST['emailReq'];


    $sql="SELECT *FROM request where saved_theme=? and AM=? ";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('si',$theme,$AM);
    if($stmt->execute()){
        $resultReq=$stmt->get_result();
        $resultaccQuery=$resultReq->fetch_assoc();
        $stmt->close();
    }else{
        $errors['select']="DB error ,Select has failed";
    }
    

    $id=$resultaccQuery['id'];
    $successfullPair=0;
    //check for duplicates 'insert'
    $sql1="SELECT *FROM pairs where id=?  ";
    $stmt1=$conn->prepare($sql1);
    $stmt1->bind_param('i',$id);
    $stmt1->execute();
    $result=$stmt1->get_result();
    $ifExists=$result->fetch_assoc();
    $stmt1->close();
    if(isset($ifExists['id'])){
        $errors['pair']="Student has agreed with another proffesor";
    }else{
        $sql1="INSERT INTO pairs(id,prof_id) VALUES (?,?)";
        $stmt=$conn->prepare($sql1);
        $stmt->bind_param('ii',$id,$prof_id);
        if($stmt->execute()){
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $errors['email']="Email address is not valid.";
            }
            if(count($errors)==0){
                acceptNotification($email);
                exit();

            }
            

            $successfullPair=1;
            $stmt->close();      
        }else{
            $errors['insert']="DB error ,Insert has failed";
        }
    }  
    if($successfullPair==1){
        $sql2="UPDATE request SET status=1 WHERE saved_theme=? and AM=?";

    }else{
        $sql2="UPDATE request SET status=4 WHERE saved_theme=? and AM=?";
    }
    $stmt=$conn->prepare($sql2);
    $stmt->bind_param('si',$theme,$AM);
    if($stmt->execute()){
        $stmt->close();    
        if($successfullPair==1) {
            //TESTING
            $file = fopen("tests/unit/acceptStudent.txt", "w") or die("Unable to open file!");
            $txt =$prof_id."\n";
            fwrite($file, $txt);
            $txt =$id."\n";
            fwrite($file, $txt);
            $status=1;
            $txt =$status."\n";
            fwrite($file, $txt);
            fclose($file);

            $_SESSION['message']="Theme accepted successfully";
        }
        header("location: professorHome.php");
    }else{
        $errors['update']="DB error ,Update has failed";
    }    
}

//reject request
if(isset($_POST['rejectRequest'])){
    $themeReq=$_POST['themeReq'];
    $AMReq=$_POST['AM'];
    $sql="UPDATE request SET status=2 WHERE saved_theme=? and AM=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('si',$themeReq,$AMReq);
    if($stmt->execute()){
        $_SESSION['message']="Theme rejected successfully";

        //testing
        $file = fopen("tests/unit/rejectStudent.txt", "w") or die("Unable to open file!");
        $txt =$AMReq."\n";
        fwrite($file, $txt);
        $txt =$themeReq."\n";
        fwrite($file, $txt);
        $status=2;
        $txt =$status."\n";
        fwrite($file, $txt);
        fclose($file);

        header("location: professorHome.php");

    }else{
        $errors['update']="DB error ,Update has failed";
    }
    $resultRejected=$stmt->get_result();
    $stmt->close();

}

//mark an accepted request as finished
if(isset($_POST['finished'])){
    $responsedCase=$_POST['responsedCase'];
    $responsedCaseAM=$_POST['responsedCaseAM'];
    $sql="UPDATE request SET status=3 WHERE saved_theme=? and AM=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('si',$responsedCase,$responsedCaseAM);
    if($stmt->execute()){
        //TESTING
        $file = fopen("tests/unit/markCaseAsFinished.txt", "w") or die("Unable to open file!");
        $txt =$responsedCase."\n";
        fwrite($file, $txt);
        $txt =$responsedCaseAM."\n";
        fwrite($file, $txt);
        $status=3;
        $txt =$status."\n";
        fwrite($file, $txt);
        fclose($file);
        $_SESSION['message']="Case marked as finished successfully";

    }else{
        $errors['update']="DB error ,Update has failed";
    }
    $resultFinished=$stmt->get_result();
    $stmt->close();
    
}


//change login data for professor
if(isset($_POST['change-btn'])){
    global $conn;
    $email=$_POST['email'];
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['email']="Email address is not valid";
    }
    if(count($errors)==0){
        $email =$conn->real_escape_string ($email);

        $sql="SELECT * FROM profusers WHERE email='$email' LIMIT 1";
        $result=mysqli_query($conn,$sql);
        $profUser=mysqli_fetch_assoc($result);
        $token=$profUser['token'];
        echo $token;
        changeProfLogData($email,$token);
        header('location:resetPasswordMessage.php');
        exit();
    }
}



if(isset($_POST['reset-btn'])){
    global $conn;
    $password=$_POST['cPassword'];
    $passwordConf=$_POST['cPasswordConf'];
    $username=$_POST['cUsername'];
    $usernameConf=$_POST['cUsernameConf'];
    /*if(empty($password)||empty($passwordConf)){
        $errors['password']="Password required";
    }*/
    if($password !== $passwordConf){
        $errors['password']="The two passwords do not match";
    }
    /*if(empty($username)||empty($usernameConf)){
        $errors['username']="Password required";
    }*/
    if($username !== $usernameConf){
        $errors['username']="The two usernames do not match";
    }
    $password=password_hash($password,PASSWORD_DEFAULT);
    $email=$_SESSION['profemail'];
    if(count($errors)==0){
        $email =$conn->real_escape_string ($email);

        $sql="UPDATE profusers SET password='$password' WHERE email='$email'";
        $sql1="UPDATE profusers SET username='$username' WHERE email='$email'";
        $result=mysqli_query($conn,$sql);
        $result1=mysqli_query($conn,$sql1);
        if($result){
            if($result1){
                header('location:login.php');
                exit;
            }
        }
    }
}

function linkToForm($token){
    global $conn;
    echo $token;
    $token =$conn->real_escape_string ($token);

    $sql="SELECT * FROM profusers WHERE token='$token' LIMIT 1";
    $result=mysqli_query($conn,$sql);
    $profUser=mysqli_fetch_assoc($result);
    //new session so i need to take the email again to make sure that there is still 
    //a user at the website (maybe account could have been deleted)
    $_SESSION['profemail']=$profUser['email'];
    echo $_SESSION['profemail'];
    header('location: changeProfDataForm.php');
    exit();
}


?>

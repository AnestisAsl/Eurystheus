<?php

require_once 'config/constants.php';
require_once 'controllers/authController.php';

//request
if(isset($_POST['sendRequest'])){
    $sql="SELECT *FROM pairs WHERE id=? ";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('i',$_SESSION['id']);
    $stmt->execute();
    $ifPair=$stmt->get_result();
    $resIfPair=$ifPair->fetch_assoc();
    $pairCount=$ifPair->num_rows;
    $stmt->close();    
    if($pairCount==1){
        $sql="SELECT prof_id FROM pairs WHERE id=? ";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('i',$_SESSION['id']);
        $stmt->execute();
        $profId=$stmt->get_result();
        $resProfId=$profId->fetch_assoc();
        $stmt->close();
        $sql="SELECT *FROM dissertation WHERE prof_id=? ";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('i',$resProfId);
        $stmt->execute();
        $disInfos=$stmt->get_result();
        $resdisInfos=$disInfos->fetch_assoc();
        $stmt->close();
        $sql="SELECT prof_name FROM profdata WHERE prof_id=? ";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('i',$resProfId);
        $stmt->execute();
        $profName=$stmt->get_result();
        $resProfName=$profName->fetch_assoc();
        $stmt->close();
        $errors['pairError']="You have already been accepted by the professor :".$resProfName["prof_name"]."for the subject:".$resdisInfos['theme'];
    }else{
        $currentTimeinSeconds = time();
        $currentDate = date('Y-m-d', $currentTimeinSeconds); 
        if($currentTimeinSeconds>=$startDateSt and $currentTimeinSeconds<=$endDateSt){
            //take the values from the request form
            $email=$_POST['email'];
            $AM=$_POST['AM'];
            $id=$_SESSION['id'];
            $saved_theme=$_POST['requestTheme'];
            $saved_description=$_POST['requestDescription'];
            $informations=$_POST['infos'];
            $name=$_POST['name'];


            //charts
            $sql="SELECT *FROM dissertation WHERE theme=?";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param('s',$saved_theme);
            $stmt->execute();
            $resultTheme=$stmt->get_result();
            $resForKeyword=$resultTheme->fetch_assoc();
            $stmt->close();

            if(isset($resForKeyword['keyword'])){
                $keyword=$resForKeyword['keyword'];
                $keyword=explode(",",$keyword);
                foreach ($keyword as $key){
                    $sql="SELECT *FROM statistics WHERE keyword=?";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param('s',$key);
                    $stmt->execute();
                    $resultKey=$stmt->get_result();
                    $keywordToInsert=$resultKey->fetch_assoc();
                    $stmt->close();
                    if(isset($keywordToInsert)){
                        $sql="UPDATE statistics SET number_with_keyword=number_with_keyword+1 WHERE keyword=?";
                        $stmt=$conn->prepare($sql);
                        $stmt->bind_param('s',$key);
                        if($stmt->execute()){
                            $stmt->close();
                        }else{
                            $errors['update']="DB error,Update has falied";
                        }
                    }else{
                        if($key!=""){
                            $sql="INSERT INTO statistics(keyword,year) VALUES (?,?)";
                            $stmt=$conn->prepare($sql);
                            $date=(int)date("Y");
                            $stmt->bind_param('si',$key,$date);
                            if($stmt->execute()){
                                $stmt->close();
                            }else{
                                $errors['insert']="DB error,Insert has falied";
                            }
                        }
                        
                    }   
                }
            }
            


            //take the prof id thanks to subject and description
            $sql="SELECT *FROM dissertation WHERE theme=? AND description=? LIMIT 1";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param('ss',$saved_theme,$saved_description);
            $stmt->execute();
            $result=$stmt->get_result();
            $prof_user=$result->fetch_assoc();
            $prof_id=$prof_user['prof_id'];
            $accepted=0;
            //insert the values to the request table
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $errors['email']="Email address is not valid";
            }else{
                $sql="SELECT *FROM request WHERE id=? and prof_id=? and saved_theme=? and saved_description=?";
                $stmt=$conn->prepare($sql);
                $stmt->bind_param('iiss',$id,$prof_id,$saved_theme,$saved_description);
                $stmt->execute();
                $ifExists=$stmt->get_result();
                $resIfExists=$ifExists->fetch_assoc();
                $stmt->close();

                //TESTING
                $file = fopen("tests/unit/sendRequest.txt", "w") or die("Unable to open file!");
                $txt =$id."\n";
                fwrite($file, $txt);
                $txt =$prof_id."\n";
                fwrite($file, $txt);
                $txt =$saved_theme."\n";
                fwrite($file, $txt);
                fclose($file);

                if(isset($resIfExists['id'])){
                    $sql="UPDATE request SET communicationemail=?,informations=?,status=0,student_name=?,AM=? WHERE id=? and prof_id=? and saved_theme=? and saved_description=?";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param('sssiiiss',$email,$informations,$name,$AM,$id,$prof_id,$saved_theme,$saved_description);
                    if($stmt->execute()){
                        $stmt->close();
                        $_SESSION['message']="Your request has been updated";

                        header("location: index.php");
                    }else{
                        $errors['update']="DB error,Update has failed";
                    }
                }else{
                    $sql="INSERT INTO request(id,prof_id,saved_theme,saved_description,communicationemail,informations,status,student_name,AM) VALUES (?,?,?,?,?,?,?,?,?)";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param('iissssisi',$id,$prof_id,$saved_theme,$saved_description,$email,$informations,$accepted,$name,$AM);
                    if($stmt->execute()){
                        $stmt->close();
                        $_SESSION['message']="Your request has been sent";
                        header("location: index.php");
                    }else{
                        $errors['insert']="DB error,Insert has failed";
                    }
                } 
            }
            //send noification email to the professor
            $theme=$_POST['requestTheme'];
            $description=$_POST['requestDescription'];
            $sql="SELECT * FROM dissertation WHERE theme='$theme' AND description='$description' LIMIT 1";
            $result=mysqli_query($conn,$sql);
            $profuser=mysqli_fetch_assoc($result);
            $prof_id=$profuser['prof_id'];
            $sql1="SELECT * FROM profusers WHERE id='$prof_id'  LIMIT 1";
            $result1=mysqli_query($conn,$sql1);
            $profuser1=mysqli_fetch_assoc($result1);
            $email=$profuser1['email'];


            //testing
            $file = fopen("tests/unit/sendEmail.txt", "w") or die("Unable to open file!");
            $txt =$profuser1['email']."\n";
            fwrite($file, $txt);
            fclose($file);

            //validation for email (no@ or.com e.t.c)
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $errors['email']="Email address is not valid";
            }
            if(count($errors)==0){
                echo($email);
                requestNotification($email);
                exit();
            }
        }else{
            $errors['dateStudent']="Date error ,you can't send a request right now.Available dates for requests from :".$startDaySt."to :".$endDaySt;
        }
    }
}



if(isset($_POST['submitFinalDecision'])){


    
}
//delete from personal list
if(isset($_POST['deleteFromList'])){
    if(isset($_POST['themeFromList'])){
        $theme=$_POST['themeFromList'];
        $sql="DELETE from personallistofdissertation WHERE id=? and theme=? LIMIT 1";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('is',$_SESSION['id'],$theme);
        if($stmt->execute()){
            $_SESSION['message']="Subject deleted sucessfully.";
            header("location: index.php");
            $stmt->close() ;
        }else{
            $errors['delete']="DB error ,Delete has failed";
        }
    }
}


if(isset($_POST['submitFeedback'])){
    $student_name=$_POST['feedbackStudentName'];
    if($student_name===""){
        $student_name="Anonymous";

    }
    $feedback=$_POST['feedback'];
    $timeSec = time();
    $date = date('Y-m-d', $timeSec); 
    recaptchaValidation();
    if(count($errors)==0){
        $sql="INSERT INTO feedback(student_name,feedback,student_id,date) VALUES (?,?,?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('ssis',$student_name,$feedback,$_SESSION['id'],$date);

        if($stmt->execute()){
            $stmt->close();
            $_SESSION['message']="Review submitted sucessfully.";
            header("location: feedback.php");
        }else{
            $errors['insert']="DB error, Insert has failed";
        }
    }
}



if(isset($_POST['submitBug'])){
    $bug=$_POST['bugText'];
    $timeSec = time();
    $date = date('Y-m-d', $timeSec); 
    $solved=0;
    recaptchaValidation();
    if(count($errors)==0){
        $sql="INSERT INTO bugs(student_id,bug,date,solved) VALUES (?,?,?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('issi',$_SESSION['id'],$bug,$date,$solved);
        if($stmt->execute()){
            $stmt->close();
            $_SESSION['message']="Bug submitted sucessfully.";
            header("location: reportABug.php");
        }else{
            $errors['insert']="DB error, Insert has failed";
        }
    }
}
if(isset($_POST['loadRequest'])){
    $_SESSION['themeToForm']=$_POST['themeFromList'];
    $_SESSION['descriptionToForm']=$_POST['descriptionText'];
    $_SESSION['requirementsToForm']=$_POST['requirementsText'];
    $sql="SELECT *FROM dissertation WHERE theme=? AND description=? LIMIT 1";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('ss',$_POST['themeFromList'],$_POST['descriptionText']);
    $stmt->execute();
    $result=$stmt->get_result();
    $prof=$result->fetch_assoc();
    $_SESSION['profIdForUploads']=$prof['prof_id'];
    header("location: formRequest.php");

}
if(isset($_POST['submitAnswers'])){
    $answersArray=array();
    for ($i=1;$i<=$_SESSION["numberOfTotalRequirements"]; $i++) {
        if(isset($_POST['yesNo'.$i])){
            if($_POST['yesNo'.$i]=='yes'){
                array_push($answersArray,$_POST['themeRec'.$i]);
            }
        }
    }
    $size=count($answersArray);

    $sql="SELECT *FROM recommendations WHERE id=? LIMIT 1";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('i',$_SESSION['id']);
    $stmt->execute();
    $result=$stmt->get_result();
    $user=$result->fetch_assoc();
    $stmt->close();
    $totalRecommendations="";
    if($size>0){
        //the error array is used just for displaying purposes
        echo    "<div class='messageAlert' style='width:100%;text-align:center;margin-left:auto;color:black;'>
                <h3 class='titleDisplay'>Recommended themes<h3>";
        foreach($answersArray as $recommendation):
            echo "<li>".$recommendation."</li>";
            $totalRecommendations .= $recommendation . ', ';
        endforeach;
        $totalRecommendations = substr($totalRecommendations, 0, -2);
    
        if(isset($user['recommendation_theme'])){
            $sql="UPDATE recommendations SET recommendation_theme=? WHERE id=?";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param('si',$totalRecommendations,$_SESSION['id']);
            if($stmt->execute()){
                $stmt->close();
            }else{
                $errors['update']="DB error,Update has falied";
            }
        }else{
            $sql="INSERT INTO recommendations(recommendation_theme,id) VALUES (?,?)";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param('si',$totalRecommendations,$_SESSION['id']);
            if($stmt->execute()){
                $stmt->close();
            }else{
                $errors['insert']="DB error, Insert has failed";
            }
        }
        echo    "<form action='recommendation.php' method='post'>
                    <button name='OKerror' class='btn loginButton' id='OKerrorId' style='z-index:1;'>OK</button>
                </form>
        </div>";
        
    }else{
        echo    "<div class='alert alert-danger' style='width:100%;text-align:center;margin-left:auto;color:black;'>
                    <h3 class='titleDisplay'>Couldn't help you with any recommendations<h3>";
        echo    "<form action='recommendation.php' method='post'>
                    <button name='OKerror' class='btn loginButton' id='OKerrorId' style='z-index:1;'>OK</button>
                </form>
        </div>";
    }
}
?>
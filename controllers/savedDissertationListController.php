
<?php

    $stud_id=$_SESSION['id'];
    // save button
    if(isset($_POST['saveButton'])){
        $theme=$_POST['theme'];
        $description=$_POST['description'];
        $requirements=$_POST['requirements'];
        if(isset($theme)&& isset($description)){
            $sql="SELECT *FROM personallistofdissertation WHERE id=? and theme=? and description=? LIMIT 1";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param('iss',$_SESSION['id'],$theme,$description);
            $stmt->execute();
            $resIfDouble=$stmt->get_result();
            $IfDouble=$resIfDouble->fetch_assoc();
            $stmt->close();
            if(isset($IfDouble)){
                $errors['sameElement']="You have already add this theme to your list";
            }else{
                $sql="INSERT INTO personallistofdissertation(id,theme,description,requirements) VALUES (?,?,?,?)";
                $stmt=$conn->prepare($sql);
                $stmt->bind_param('isss',$stud_id,$theme,$description,$requirements);
                if($stmt->execute()){
                    //$user_id=$conn->insert_id;  
                    $stmt->close();
                    $_SESSION['message']="Subject added successfully";

                    //testing
                    $file = fopen("tests/unit/saveDissertation.txt", "w") or die("Unable to open file!");
                    $txt =$stud_id."\n";
                    fwrite($file, $txt);
                    $txt =$theme."\n";
                    fwrite($file, $txt);
                    $txt =$requirements."\n";
                    fwrite($file, $txt);
                    fclose($file);

                    header("location: dissertationDisplay.php");
                }else{
                    $errors['insert']="DB error, Insert has failed";
                }
            }
        }else{
            $errors['theme']="Choose a subject first";
        }    
    }
            
    //remove button
    if(isset($_POST['removeButton'])){
        $themeToRemove=$_POST['theme'];
        $sql="SELECT *FROM personallistofdissertation WHERE id=? and theme=?";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('is',$_SESSION['id'],$themeToRemove);
        if($stmt->execute()){

        }else{
            $errors['select']="DB error, Select has failed";
        }
        $resultToRemove=$stmt->get_result();
        $resultToRemoveIfExists=$resultToRemove->fetch_assoc();
        if(isset($resultToRemoveIfExists['theme'])){
            $sql="DELETE from personallistofdissertation WHERE theme=? and id=? LIMIT 1";
            $stmt1=$conn->prepare($sql);
            $stmt1->bind_param('si',$themeToRemove,$_SESSION['id']);
            if($stmt1->execute()){
                $stmt1->close();
                $stmt->close();
                $_SESSION['message']="Subject removed successfully";

                header("location: dissertationDisplay.php");
            }else{
                $errors['delete']="DB error, Delete has failed";
            }
        }else{
            $errors['theme']="This subject is not at your list yet";
        }
    }
    
?>
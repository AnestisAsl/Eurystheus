<?php 
require_once 'controllers/authController.php';
require_once 'controllers/profHomeController.php';


if(isset($_GET['password-token'])){
    $passwordToken=$_GET['password-token'];
    linkToForm($passwordToken);
}

//if the user is not logged in can't see the index page
//example:log in and then delete history.Should log in again.
if(!isset($_SESSION['profid'])){
    header('location:login.php');
    exit();
}
$professorId =$conn->real_escape_string ($_SESSION['profid']);

$sql="SELECT *FROM saveddissertation where prof_id='$professorId'";
$conn->prepare($sql);
$resultSavedThemes = mysqli_query($conn,$sql) or ($errors['select']="DB error ,Select has failed");
$prof_id = $saved_theme = $saved_description = array();

$sql="SELECT *FROM dissertation where prof_id=$professorId";
$conn->prepare($sql);
$resultPublishedThemes = mysqli_query($conn,$sql) or ($errors['select']="DB error ,Select has failed");
$profId = $savedTheme = $savedDescription = array();


$sql="SELECT *FROM profdata where prof_id=?";
$stmt=$conn->prepare($sql);
$stmt->bind_param('i',$_SESSION['profid']);
if($stmt->execute()){
    $resultName=$stmt->get_result();
    $name=$resultName->fetch_assoc();
    $stmt->close();      
}else{
    $errors['select']="DB error ,Insert has failed";
}
if(isset($name["prof_name"])){
    $FLname = explode(" ",$name["prof_name"]);
}

$sql1="SELECT *FROM request where prof_id=$professorId";
$conn->prepare($sql1);
$resultRequestForNot = mysqli_query($conn,$sql1) or ($errors['select']="DB error ,Select has failed");

$sql="SELECT *FROM dissertation where prof_id=$professorId";
$conn->prepare($sql);
$resultPublishedThemes2 = mysqli_query($conn,$sql) or ($errors['select']="DB error ,Select has failed");



if(isset($_POST['unsetMessage'])) {
    unset($_SESSION['message']);
    $state="message unset";
    $reaction[]=array("m"=>$state);
    exit(json_encode($reaction));

}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link rel="stylesheet" href="cssFiles/style.css">
    <link rel="stylesheet" href="cssFiles/styleMenu.css">
    <link rel="stylesheet" href="cssFiles/buttonstyle.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="images/eu1.jpg">

</head>
    
<body>
    <div class="flex-container" id="flexContainerId" style="align-items:center;">
    <div class="preload">
        <h3>Loading</h3>
        <div class="loader">
            <div class="circle"></div>
            <div class="circle"></div>
        </div>
    </div>
    <header id="profHeader">
        <div class="logo">Eurystheus</div>
        <label for="btn" class="iconMenu">
            <span class="fa fa-bars"></span>
        </label>
        <input type="checkbox" id="btn" >
        <ul>
            <li><a><i class="fas fa-exchange-alt"></i>See my...</a>
                <label for="btn-1" class="show">See my...</label>
                <input type="checkbox" id="btn-1" >
                <ul>
                    <li><a href="#" id='show-publishedThemes'><i class="fas fa-upload"></i>Public Themes</a></li>
                    <li><a href="#" id='show-table'><i class="fas fa-lightbulb"></i>Saved themes</a>
                    <li><a href="#" id='show-history'><i class="fas fa-history"></i>My History</a></li>
                </ul>
            </li>
            <li><a><i class="fas fa-exchange-alt"></i>Change</a>
                <label for="btn-2" class="show">Change</label>
                <input type="checkbox" id="btn-2" >
                <ul>
                    <li><a href="changeProfLogin.php"><i class="fas fa-user-shield"></i>Password and Username</a>
                    <li><a href="#" id="open-menu"><i class="fas fa-user-tie"></i>Name</a></li>
                </ul>
            </li>
            <li><a href="#" id="help"><i class="fas fa-info-circle"></i>Help</a>
                <label for="btn-4" class="show">Help</label>
                <input type="checkbox" id="btn-4" >
                <ul>
                    <li><a href="#" id='show-tutorial'><i class="fas fa-video"></i>Video Tutorial</a></li>
                </ul>
            </li>
            <li><a><i class="fas fa-exchange-alt"></i>Requests</a>
                <label for="btn-3" class="show">Requests</label>
                <input type="checkbox" id="btn-3" >
                <ul>
                    <li><a href="#" id='show-responsedRequest'><i class="fas fa-reply-all"></i>Responsed RequestS</a> 
                    <li><a href="#" id="show-request"> <i class="fa fa-bell"></i>Requests<span class="badge badge-light">
                    <?php
                $notificationCounter=0;
                while ($row = mysqli_fetch_assoc($resultRequestForNot)) {
                    //status==0 =>unread , status==1 => accept status==2=>reject status==3=>finished
                    if($row['status']==0){
                        $notificationCounter+=1;
                    }
                }   
            echo $notificationCounter?></span></a>
                </ul>
            </li>
    </ul>
    
    </header>
    

    <nav class="profNav" id="overlay">
        <div class="flex-container">
            <h1 class="titleDisplay">First and Last Name</h1>            
            <p class="message">Please submit your name so it can be displayed next to the themes, you publish</p>
            <form action="professorHome.php" method="post" class="navForm">
                <div class="flex-container" style="height:unset!important">
                    <div class="username">
                        <input type="text" name="firstname" value="<?php if(isset($FLname)){ echo $FLname[0];}?>" autocomplete="off" required>
                        <label for="firstname" class="usernameLabel"> 
                            <span class="contentUsername">First Name</span>
                        </label>
                    </div>
                    <div class="username">
                        <input type="text" name="lastname"value="<?php if(isset($FLname)){ echo  $FLname[2];}?>"  autocomplete="off" required>
                        <label for="lastname" class="usernameLabel"> 
                            <span class="contentUsername">Last Name</span>
                        </label>
                    </div>
                    
                    <div>
                        <button class="submit" id="submit-close-menu" name="submit">Submit</button>
                    </div>
                </div>
            </form>
            <div style="margin-left:auto;">
                <button class="closeList" id="close-menu" name="close">Close</button>
            </div>
        </div>
    </nav> 

    <nav class="profNav" id="helpTutorialId">
        <div class="flex-container" style="align-items:center;">
            <h1 class="titleDisplay">Video Tutorial</h1>            
            <p class="message">Here you can find a video of tutorial on how to use your options with Eurystheus</p>
            <div>
                <video src="tutorials/professorTutorial.mp4" controls poster="images/eu1.jpg"></video>
            </div>
            <div style="margin-left:auto;">
                <button class="closeList" name="closeTutorials" id="close-tutorials">Close</button>
            </div>
        </div>
    </nav>

    
    <nav class="profNav" id="overlaySavedThemes">
        <div class="flex-container">
                <h1 class="titleDisplay">Saved Subjects</h1>            

                <table id="table">
                    <tr>
                        <th>Professor Name</th>
                        <th>Title</th>
                        <th>Description</th>
                    </tr>
                    <?php 
                    while($row = mysqli_fetch_assoc($resultSavedThemes)) {
                            $prof_id[] = $row['prof_id'];
                            $saved_theme[] = $row['saved_theme'];
                            $saved_description[] = $row['saved_description'];
                            if(count($saved_description)>0):    
                                foreach($saved_description as $word):
                                    $changeLinePointer=explode("\n",$word);     
                                endforeach;          
                            endif;
                            echo "<tr><td>".$name["prof_name"]."</td><td>".$row['saved_theme']."</td>";
                            ?><td><?php
                            foreach($changeLinePointer as $line):
                                echo $line;  
                                ?><br><?php
                            endforeach;
                            ?></td></tr><?php
                    }
                    ?>
                </table>
                    <form action="professorHome.php" method=post>
                        <div class="flex-container">
                            <div>
                                <input type="text" class="readonly" name="themeFromListProf" id="themeFromListProf" placeholder="Click to load title here..." autocomplete="off"  required>
                            </div>
                            <div>
                                <button class="closeList" id="delete-table" name="delete">Delete</button>
                            </div>
                        </div>
                    </form>   
                <div>
                    <button class="submit" id="load-table" name="load" >Load</button>
                </div> 
                <form action="pdfSavedThemes.php">
                    <div>
                        <button class="closeList" id="pdfId" name="pdfBtnSavedThemes">Extract pdf</button>
                    </div>
                </form> 
                <div style="margin-left:auto;">       
                    <button class="closeList" id="close-table" name="close">Close</button>
                </div>
        </div>
    </nav>

    <nav class="profNav" id="overlayPublishedThemes">
        <div class="flex-container">
            <h1 class="titleDisplay">Published Subjects</h1>            

            <table id="publishedThemesTable">
                    <tr>
                        <th>Professor Name</th>
                        <th>Title</th>
                        <th>Description</th>
                    </tr>
                <?php 
                    while($row = mysqli_fetch_assoc($resultPublishedThemes)) {
                        $profId[] = $row['prof_id'];
                        $savedTheme[] = $row['theme'];
                        $savedDescription[] = $row['description'];
                        if(count($savedDescription)>0):    
                            foreach($savedDescription as $word):
                                $changeLinePointer=explode("\n",$word);     
                            endforeach;          
                        endif;
                        echo "<tr><td>".$name["prof_name"]."</td><td>".$row['theme']."</td>";
                        ?><td><?php
                        foreach($changeLinePointer as $line):
                            echo $line;  
                            ?><br><?php
                        endforeach;
                        ?></td></tr><?php
                    } 
                ?>
            </table>
            
            <form action="professorHome.php" method=post>
                <div class="flex-container">
                    <div>
                        <input type="text" class="readonly" id='publishedThemeLoadedId' name="publishedThemeLoaded" placeholder="click for load here..."   required>
                    </div>
                    <div>
                        <button class="closeList" id="deletePublishedThemeId" name="deletePublishedTheme">Delete</button>
                    </div>
                </div>
            </form>   
            <form action="pdfPublishedThemes.php">
                <div>
                    <button class="closeList" id="pdfId" name="pdfBtnPublishedThemes">Extract pdf</button>
                </div>
            </form> 
            <div style="margin-left:auto;">       
                <button class="closeList" id="close-publishedThemes-overlay" name="close">Close</button>
            </div>
        </div>
    </nav>

    <nav class="profNav" id="overlayRequest">
        <div class="flex-container">
            <h1 class="titleDisplay">Requests</h1>            

            <table id="requestTable">
                <tr>
                    <th>Request Title</th>
                    <th>Student AM</th>
                </tr>
                <?php 
                    $sql1="SELECT *FROM request where prof_id=$_SESSION[profid] and status=0";
                    $conn->prepare($sql1);
                    $resultRequestForOverlay = mysqli_query($conn,$sql1) or ($errors['select']="DB error ,Select has failed");
                    
                    while($row = mysqli_fetch_assoc($resultRequestForOverlay)) {  
                        $sql="SELECT *FROM users where id=?";
                        $stmt=$conn->prepare($sql);
                        $stmt->bind_param('i',$row['id']);
                        if($stmt->execute()){
                            $resultAM=$stmt->get_result();
                            $student=$resultAM->fetch_assoc();
                            $stmt->close();      
                        }else{
                            $errors['select']="DB error ,Insert has failed";
                        }
                        echo "<tr><td>".$row["saved_theme"]."</td><td>".$student['AM']."</td></tr>";
                    }
                ?>  
            </table>
            <form action="professorHome.php" method="post">
                <div>
                    <input type="text" name="request" id="requestId" class="readonly" placeholder="click for request here..." autocomplete="off" required>
                    <input type="text" name="requestAM" id="amId" class="readonly" placeholder="click for request here..." autocomplete="off" required>
                </div>
                <div>
                    <button class="submit" name="loadRequest"> Load</button>
                </div>
            </form>
            <div style="margin-left:auto;">
                <button class="closeList" id="close-request" name="closeRequest">Close</button>
            </div>
        </div>
    </nav>



    <nav class="profNav" id="overlayResponsedRequest">
        <div class="flex-container">
            <h1 class="titleDisplay">Responsed Request</h1>            
            <h2>ACCEPTED STUDENTS</h2>
            <table id="acceptedRequestTable">
                <tr>
                    <th>Request Title</th>
                    <th>Student AM</th>
                    <th>Student name</th>
                </tr>
                <?php 
                    $sql="SELECT *FROM request where prof_id=$_SESSION[profid] and status=1";
                    $conn->prepare($sql);
                    $acceptedRequest = mysqli_query($conn,$sql) or ($errors['select']="DB error ,Select has failed");
                    $acceptedRequestResult = mysqli_query($conn,$sql) or ($errors['select']="DB error ,Select has failed");
                    
                    while($row = mysqli_fetch_assoc($acceptedRequestResult)) {  
                        echo "<tr><td>".$row["saved_theme"]."</td><td>".$row['AM']."</td><td>".$row['student_name']."</td></tr>";
                    }
                ?>  
            </table>
            <form action="professorHome.php" method=post>
                <input type="text" name="responsedCase" id="responsedCaseId" class="readonly" placeholder="click for load here..." autocomplete="off" required>
                <div>
                    <input type="text" name="responsedCaseAM" id="responsedCaseAMId" class="readonly" placeholder="click for load here..." autocomplete="off" required>
                </div>
                <button class="submit" name="finished">Mark this as finished</button>
            </form>
            <h2>REJECTED STUDENTS</h2>
            <table id="rejectedRequestTable">
                <tr>
                    <th>Request Title</th>
                    <th>Student AM</th>
                    <th>Student name</th>
                </tr>
                <?php 
                    $sql="SELECT *FROM request where prof_id=$_SESSION[profid] and status=2";
                    $conn->prepare($sql);
                    $rejectedRequest = mysqli_query($conn,$sql) or ($errors['select']="DB error ,Select has failed");
                    $rejectedRequestResult = mysqli_query($conn,$sql) or ($errors['select']="DB error ,Select has failed");  
                    while($row = mysqli_fetch_assoc($rejectedRequestResult)) {  
                        echo "<tr><td>".$row["saved_theme"]."</td><td>".$row['AM']."</td><td>".$row['student_name']."</td></tr>";
                    }
                ?>  
            </table>
            <div style="margin-left:auto;">
                <button class="closeList" id="close-responsedRequest" name="closeResponsedRequest">Close</button>  
            </div> 
        </div>      
    </nav>



    <nav class="profNav" id="overlayHistory">
        <div class="flex-container">
            <h1 class="titleDisplay">Record</h1>            

            <table id="historyTable">
                <tr>
                    <th>Request Title</th>
                    <th>Student AM</th>
                    <th>Student name</th>
                </tr>
                <?php 
                    $sql1="SELECT *FROM request where prof_id=$_SESSION[profid] and status=3";
                    $conn->prepare($sql1);
                    $finished = mysqli_query($conn,$sql1) or ($errors['select']="DB error ,Select has failed");
                    $finishedResult = mysqli_query($conn,$sql1) or ($errors['select']="DB error ,Select has failed");
                    
                    while($row = mysqli_fetch_assoc($finishedResult)) {  
                        echo "<tr><td>".$row["saved_theme"]."</td><td>".$row['AM']."</td><td>".$row['student_name']."</td></tr>";
                    }
                ?>  
            </table>
            <form action="pdf.php" method="post">
                <button class="closeList" id="pdfId" name="pdfBtn">Extract pdf</button>
            </form>
            <div style="margin-left:auto;">
                <button class="closeList" id="close-history" name="closeHistory">Close</button>
            </div>
        </div>
    </nav>

    
    <div>
        <?php if(count($errors)>0): ?>
            <div class="alert alert-danger" style="position:absolute;left:250px;">
            <?php foreach($errors as $error):?>
                <li><?php echo $error;?></li>
            <?php endforeach;?>
            <form action="professorHome.php" method="post">
                <button name="OKerror" class="btn loginButton" id="OKerrorId" style="z-index:1;">OK</button>
            </form>
    </div>
    <?php endif;?>

    <?php if(isset($_SESSION['message'])):?>
        <div class="messageAlert" style="width:auto;">
            <li><?php echo $_SESSION['message'];?></li>
                <button name="OKmessage" class="btn loginButton" id="OKmessageID"  style="z-index:1;">OK</button>
        </div>
    <?php endif;?>

    </div>
    <form action ="professorHome.php" method="post" class="professorHomeContainer">
        <div>
            <div>
                <input type="text" name="theme" id="themeId" placeholder="Write title here..." autocomplete="off" required>
            </div>
            <div class="textarea">
                <textarea name="description" id="descriptionId" cols="50" rows="10" placeholder="Write descritption here..." autocomplete="off" required></textarea>
            </div>
            <div>
                <p class="message">Add prerequisites</p>
                <p class="message">The student should know :</p>
                <i id="addRequirementsId" class="fas fa-plus fa-2x"></i>
                <i id="removeRequirementsId" class="fas fa-minus fa-2x"></i>
                <ul id="requirementsId">
                </ul>
                <p class="message">The dissertation is related to my Undergraduate Courses
                (optional)<br></p>
                <input type="radio" name="yesNo" value='yes'>Yes
                <input type="radio" name="yesNo" value='no'>No
            </div>

            <div>
                <button type="submit" name="saveButton" class="btn loginButton">Save</button>
                <button type="submit" name="publishDissertation" class="btn loginButton">publish Dissertation</button>
            </div>
        </div>
        <div class="keywords">
                <p class="message">Add CS field here</p>
                <input type="checkbox" name="keyword[]" value="Data Management and Graphics" id="key">Data Management and Graphics<br>
                <input type="checkbox" name="keyword[]" value="Networks and Telecommunications" id="key"> Networks and Telecommunications<br>
                <input type="checkbox" name="keyword[]" value=" Artificial Intelligence and Robotics" id="key"> Artificial Intelligence and Robotics<br>
                <input type="checkbox" name="keyword[]" value="Software systems" id="key">Software systems<br>
                <input type="checkbox" name="keyword[]" value=" Algorithm Technology and Theory" id="key"> Algorithm Technology and Theory<br>
                <input type="checkbox" name="keyword[]" value=" Material Technologies and Computer Architecture" id="key"> Material Technologies and Computer Architecture<br>
                <input type="checkbox" name="keyword[]" value="Students are allowed to cooperate(pair dissertation)" id="key">Students are allowed to cooperate(pair dissertation)<br>

                <p class="message">My CS field </p>
                <?php
                    $sql="SELECT prof_keywords FROM profdata where prof_id=? ";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param('i',$_SESSION['profid']);
                    if($stmt->execute()){
                        $otherKeyword=$stmt->get_result();
                        $otherKeywordResult=$otherKeyword->fetch_assoc();
                        $stmt->close();      
                    }else{
                        $errors['select']="DB error ,Select has failed";
                    }
                    if(isset($otherKeywordResult['prof_keywords'])){
                        $splittedOtherKeywordResult=explode(",",$otherKeywordResult['prof_keywords']);
                        foreach ($splittedOtherKeywordResult as $str){ 
                            if($str!=""){
                            echo "<input type='checkbox' name='keyword[]' class='myKeyword' id='key'  onchange='toggleCheckbox(this)' value='$str'>".$str."<br>";
                            }
                        } 
                    }
                ?>
    </form>
            <div class="otherKeyword">
                <form action="professorHome.php" method="post">
                    <input type="text" name="otherKeyword" id="otherId" placeholder="Other..." autocomplete="off" required>
                    <button type="submit" name="addKeyword" id="otherButtonId" class="btn loginButton">Add CS field</button>
                    <button type="submit" name="removeKeyword" id="otherRmvButtonId" class="logout" >Remove CS field</button>
                </form>
            </div>
                    
            <form action ="professorHome.php" method="post"  style="float:right;">
                <div>
                    <button type="submit" name="logoutButtonProf" id="logoutButtonProfId" class="logout">logout</button>
                </div>
            </form>
        </div>
        
        
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        //responsive purposes
        heightOfNavs=$("nav").height();
        if(heightOfNavs<2*$(window).height()){
            $("nav").height(2*$(window).height()+100);
        }
        if($(window).width()>=900){
            $("#logoutButtonProfId").css("top",$(window).height()/4);
        }
        /*for responsive purposes*/
        var btn=document.getElementById('btn');
        $('#btn').click(function(){
            if (document.getElementById('btn').checked) {
                $(".professorHomeContainer").addClass("changePosOfProfessorHomeContainer");
            }
            else {
                $(".professorHomeContainer").removeClass("changePosOfProfessorHomeContainer");
            };            
        });
        //#show-tableRequest
        $('ul li ul li').click(function(){
            setTimeout(function() {
                $("#professorHomeContainerID,.professorHomeContainer").hide()
            }, 1000);
            setTimeout(function() {
                $("li").hide()
            }, 1000);
            $(":button").click(function(){
                $("#professorHomeContainerID,.professorHomeContainer").show();
            }); 
            $(":button").click(function(){
                $("li").show();
            }); 
        }); 

        
        var overlay=document.getElementById('overlay');
        var closeMenu=document.getElementById('close-menu');
        var submitCloseMenu=document.getElementById('submit-close-menu');
        document.getElementById('open-menu').addEventListener('click',function(){
            overlay.classList.add('show-menu');
        });
        document.getElementById('close-menu').addEventListener('click',function(){
            overlay.classList.remove('show-menu');
        });
        document.getElementById('submit-close-menu').addEventListener('click',function(){
            overlay.classList.remove('show-menu');
        });

        var overlaySavedThemes=document.getElementById('overlaySavedThemes');
        document.getElementById('show-table').addEventListener('click',function(){
            overlaySavedThemes.classList.add('show-menu');
        });
        document.getElementById('close-table').addEventListener('click',function(){
            overlaySavedThemes.classList.remove('show-menu');
        });
        
        var overlayRequest=document.getElementById('overlayRequest');
        document.getElementById('show-request').addEventListener('click',function(){
            overlayRequest.classList.add('show-menu');
        });
        document.getElementById('close-request').addEventListener('click',function(){
            overlayRequest.classList.remove('show-menu');
        });
        var overlayResponsedRequest=document.getElementById('overlayResponsedRequest');
        document.getElementById('show-responsedRequest').addEventListener('click',function(){
            overlayResponsedRequest.classList.add('show-menu');
        });
        document.getElementById('close-responsedRequest').addEventListener('click',function(){
            overlayResponsedRequest.classList.remove('show-menu');
        });
        
        var overlayHistory=document.getElementById('overlayHistory');
        document.getElementById('show-history').addEventListener('click',function(){
            overlayHistory.classList.add('show-menu');
        });
        document.getElementById('close-history').addEventListener('click',function(){
            overlayHistory.classList.remove('show-menu');
        });
        

        var helpTutorial=document.getElementById('helpTutorialId');
        document.getElementById('show-tutorial').addEventListener('click',function(){
            helpTutorial.classList.add('show-menu');
        });
        document.getElementById('close-tutorials').addEventListener('click',function(){
            helpTutorial.classList.remove('show-menu');
        });

        var overlayPublishedThemes=document.getElementById('overlayPublishedThemes');
        document.getElementById('show-publishedThemes').addEventListener('click',function(){
            overlayPublishedThemes.classList.add('show-menu');
        });
        document.getElementById('close-publishedThemes-overlay').addEventListener('click',function(){
            overlayPublishedThemes.classList.remove('show-menu');
        });

       var index,table=document.getElementById('table');
       var load=document.getElementById('load-table'); 
       var themeFromListProf=document.getElementById('themeFromListProf'); 
       var mirror="";
       var mirrorTheme='';
       for(var i=1;i<table.rows.length;i++){
            table.rows[i].onclick=function(){
                tempHoldCellThemeValue=this.cells[1].innerHTML;
                tempHoldCellDescValue=this.cells[2].innerHTML;
                mirror=tempHoldCellDescValue;
                mirrorTheme=tempHoldCellThemeValue
                tempHoldCellDescValue=mirror.replace(/<br>/g,'');
                tempHoldCellThemeValue=mirrorTheme.replace(/<br>/g,'');
                themeFromListProf.value=tempHoldCellThemeValue;
                load.onclick=function(){
                    document.getElementById('themeId').value=tempHoldCellThemeValue;
                    document.getElementById('descriptionId').value=tempHoldCellDescValue;
                    overlaySavedThemes.classList.remove('show-menu');
                }
                
                if(typeof index !== "undefined"){
                    table.rows[index].classList.toggle("selectedRow");
                }
                index=this.rowIndex;
                this.classList.toggle("selectedRow");
               
            }
        }

       var requestTable=document.getElementById('requestTable');
       var requestId=document.getElementById('requestId'); 
       var amId=document.getElementById('amId'); 
       for(var i=1;i<requestTable.rows.length;i++){
        requestTable.rows[i].onclick=function(){
                requestId.value=this.cells[0].innerHTML;
                amId.value=this.cells[1].innerHTML;
            }
        }

        var acceptedRequestTable=document.getElementById('acceptedRequestTable');
        var responsedCaseId=document.getElementById('responsedCaseId'); 
        var responsedCaseAMId=document.getElementById('responsedCaseAMId'); 
        for(var i=1;i<acceptedRequestTable.rows.length;i++){
            acceptedRequestTable.rows[i].onclick=function(){
                responsedCaseId.value=this.cells[0].innerHTML;
                responsedCaseAMId.value=this.cells[1].innerHTML;
            }
        }

        var publishedThemesTableMore=document.getElementById('publishedThemesTableMore');
        var publishedThemesTable=document.getElementById('publishedThemesTable');
        var publishedThemeLoadedId=document.getElementById('publishedThemeLoadedId'); 
        for(var i=1;i<publishedThemesTable.rows.length;i++){
            publishedThemesTable.rows[i].onclick=function(){
                publishedThemeLoadedId.value=this.cells[1].innerHTML;
            }
        }
        


        //can't write at an object with 'readonly' attribute
        $(".readonly,.readOnlyMessage").keydown(function(e){
            e.preventDefault();
        });

        //selected rows getting colored
        $('table tr').click(function () {
            $('table tr').each(function (a) {
                $(this).removeClass('selectedRow')
            });
            $(this).addClass('selectedRow');
        });

        //fill the other keyword input when he checks at 'my key words' keywords
        function toggleCheckbox(element)
        {
            var otherId=document.getElementById('otherId');
            if(element.checked==true){
                otherId.value = element.value;
            }else{
                otherId.value="";
            }
        }
        //add requirements
        $("#addRequirementsId").click(function(){
            $("#requirementsId").append('<li><input name=reqName[] id=reqInputId class=inputStyle style=width:60% required autocomplete="off"></li>');
        });
        
        $("#removeRequirementsId").click(function(){
            $("#requirementsId li:last-child").remove();
        });


        window.addEventListener('load',() =>{
            const preload=document.querySelector('.preload');
            preload.classList.add('preload-finish');
        });
    $('#OKmessageID').click(function(){
        unsetMessage();
    });
    function unsetMessage(){
        $.ajax({
            url: 'professorHome.php',
            type: 'post',
            dataType:'json',
            data: {
                'unsetMessage': 1,
            },
            success: function(response){
                console.log("success");
                $(".messageAlert").hide();
            },
            error: function(response){
                console.log("error");
            }
        });
    }
    </script>
</div>
</body>
</html>
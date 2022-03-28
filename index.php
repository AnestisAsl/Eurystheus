
<?php 

require_once 'controllers/authController.php';
require_once 'controllers/studentHomeController.php';

if(isset($_GET['token'])){
    $token=$_GET['token'];
    verifyUser($token);
}

if(isset($_GET['password-token'])){
    $passwordToken=$_GET['password-token'];
    resetPassword($passwordToken);
}
if(!isset($_SESSION['id'])){
    header('location:login.php');
    exit();
}

$studentId =$conn->real_escape_string ($_SESSION['id']);

$sql="SELECT *FROM personallistofdissertation where id='$studentId'";
$conn->prepare($sql);
$resultList = mysqli_query($conn,$sql) or /*errorprinter*/($errors['select']="DB error ,Select has failed");


$sql="SELECT *FROM request where id=$studentId";
$conn->prepare($sql);
$resultReq = mysqli_query($conn,$sql) or ($errors['select']="DB error ,Select has failed");

$sql1="SELECT *FROM request where id=$studentId";
$conn->prepare($sql1);
$resultRequestNotification = mysqli_query($conn,$sql1) or ($errors['select']="DB error ,Select has failed");

$sql1="SELECT *FROM recommendations where id=$studentId";
$conn->prepare($sql1);
$recommendations = mysqli_query($conn,$sql1) or ($errors['select']="DB error ,Select has failed");



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
    <!--For responsive design-->
    <meta name=viewport content="width=device-width,initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" >
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link rel="stylesheet" href="cssFiles/style.css">
    <link rel="stylesheet" href="cssFiles/styleMenu.css">
    <link rel="stylesheet" href="cssFiles/buttonstyle.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="images/eu1.jpg">
    <title>Home</title>
</head>

    
<body>
<div class="flex-container" style="justify-content: space-between;align-items: center;">

    <div class="preload">
        <h3>Loading</h3>
        <div class="loader">
            <div class="circle"></div>
            <div class="circle"></div>
        </div>
    </div>
    <header style="position:unset!important">
        <div class="logo">Eurystheus</div>
        <label for="btn" class="iconMenu">
            <span class="fa fa-bars"></span>
        </label>
        <input type="checkbox" id="btn" >
        <ul>
            <li><a><i class="fas fa-paste"></i>See my ...</a>
                <label for="btn-1" class="show">See my ...</label>
                <input type="checkbox" id="btn-1">
                <ul>
                    <li><a href="#"  id="show-list"><i class="fas fa-list-ol"></i>List</a></li>
                    <li><a href="#" id="show-requests"><i class="fas fa-list-alt"></i>Requests</a><span class="badge badge-light">
                    <?php
                $notificationCounter=0;
                while ($row = mysqli_fetch_assoc($resultRequestNotification)) {
                    //status==0 =>unread , status==1 => accept status==2=>reject status==3=>finished
                    if($row['status']==1){
                        $notificationCounter+=1;
                    }
                }   
            echo $notificationCounter?></span></a></li>   
                </ul>
            </li>
            <li><a><i class="fas fa-exchange-alt"></i>Reset</a>
                <label for="btn-2" class="show">Reset</label>
                <input type="checkbox" id="btn-2" >
                <ul>
                    <li><a href="forgotPassword.php"><i class="fas fa-unlock"></i>Password</a></li>
                </ul>
            </li>
            <li><a><i class="fas fas fa-info-circle"></i>Help</a>
                <label for="btn-4" class="show">Help</label>
                <input type="checkbox" id="btn-4">
                <ul>
                    <li><a href=# id="show-tutorials"><i class="fas fa-info"></i>Tutorials</a></li>
                    <li><a href=# id="show-paper"><i class="fas fa-scroll"></i>Report</a></li>
                    <li ><a style="font-size:17px;" href=# id="show-recommendation"><i class="fas fa-hands-helping"></i>Recommended</a></li>

                </ul>
            </li>
            <li><a><i class="fas fa-caret-square-down"></i>More</a>
                <label for="btn-3" class="show">More</label>
                <input type="checkbox" id="btn-3">
                <ul>
                    <li><a href="reportABug.php"><i class="fas fa-bug"></i>Report a bug</a></li>
                    <li><a href="feedback.php"><i class="fas fa-comments"></i>Give us feedback</a></li>
                </ul>
            </li>
        </ul>
    </header>
    <nav id="overlayList">
        <div class="flex-container">
            <h1 class="titleDisplay">Your subjects </h1>            

            <table id="table">
                <tr>
                    <th>Professor Name</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Prerequisites</th>
                </tr>
                <?php 
                while($row = mysqli_fetch_assoc($resultList)) {
                    $theme[] = $row['theme'];
                    $description[] = $row['description']; 
                    $sql="SELECT *FROM dissertation where theme=? ";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param('s',$row['theme']);
                    if($stmt->execute()){
                        $resultForProfID=$stmt->get_result();
                        $profID=$resultForProfID->fetch_assoc();
                        $stmt->close();      
                    }else{
                        $errors['select']="DB error ,Insert has failed";
                    } 
                    $sql="SELECT *FROM profdata where prof_id=? ";
                    $stmt=$conn->prepare($sql);
                    $stmt->bind_param('i',$profID['prof_id']);
                    if($stmt->execute()){
                        $resultForName=$stmt->get_result();
                        $profName=$resultForName->fetch_assoc();
                        $stmt->close();      
                    }else{
                        $errors['select']="DB error ,Insert has failed";
                    }
                    if(count($description)>0):    
                        foreach($description as $word):
                            $changeLinePointer=explode("\n",$word);     
                        endforeach;          
                    endif;
                    echo "<tr><td>".$profName['prof_name']."</td><td>".$row['theme']."</td>";
                    ?><td><?php
                    foreach($changeLinePointer as $line):
                        echo $line;  
                        ?><br><?php
                    endforeach;
                    ?></td><?php echo "<td>".$row['requirements']."</td>"?></tr><?php
                }
                ?>
            </table> 
            <form action="index.php" method="post">
                <div class="flex-container">

                    <div>
                        <input type="text" class="readonly" name="themeFromList" id="themeFromList" placeholder="Click to load theme here..." autocomplete="off"  required>
                    </div>
                    <textarea name="descriptionText" class="readonly" id="descriptionTextId" cols="50" rows="5" placeholder="Load descritption here..." autocomplete="off" style="display:none" required ></textarea>
                    <textarea name="requirementsText" class="readonly" id="requirementsTextId" cols="20" rows="5" placeholder="Load requirements here..." autocomplete="off" style="display:none" ></textarea>
                    <div>
                        <button class="submit" name="loadRequest" id="loadRequestId">Form a Request</button>
                    </div>
                    <div style="margin-right:auto;">
                        <button class="closeList" name="deleteFromList" id="deleteFromListID">Delete</button>
                    </div>
                    
                </div>
            </form>
            <div style="margin-left:auto;">
                <button class="closeList" name="closeList" id="close-list">Close</button>
            </div>
        </div>      
    </nav> 

    <nav id="overlayRequests">
        <div class="flex-container">
            <h1 class="titleDisplay">Your Requests </h1>            

            <table id="tableReq">
                <tr>
                    <th>Professor Name</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>State</th>
                </tr>
                <?php 
                    while($row = mysqli_fetch_assoc($resultReq)) {
                        $theme[] = $row['saved_theme'];
                        $description[] = $row['saved_description'];
                        $profID=$row['prof_id'];
                        $state=$row['status'];
                        $sql="SELECT *FROM profdata where prof_id=? ";
                        $stmt=$conn->prepare($sql);
                        $stmt->bind_param('i',$profID);
                        if($stmt->execute()){
                            $resultForName=$stmt->get_result();
                            $profName=$resultForName->fetch_assoc();
                            $stmt->close();      
                        }else{
                            $errors['select']="DB error ,Insert has failed";
                        }
                        if(count($description)>0):    
                            foreach($description as $word):
                                $changeLinePointer=explode("\n",$word);     
                            endforeach;          
                        endif;
                        echo "<tr><td>".$profName['prof_name']."</td><td>".$row['saved_theme']."</td>";
                        ?><td><?php
                        foreach($changeLinePointer as $line):
                            echo $line;  
                            ?><br><?php
                        endforeach;
                        ?></td>
                        <?php
                        if($state==1){
                            $reqResult="Accepted";
                        }else if($state==2){
                            $reqResult="Rejected";
                        }else if($state==4){
                            $reqResult="Accepted later";
                        }else if($state==3){
                            $reqResult="Professor marked this as finished";
                        }else{
                            $reqResult="Still pedding";
                        }
                        echo "<td>".$reqResult."</td>"?>
                        </tr><?php
                    }
                    ?>
            </table> 
            <div style="margin-left:auto;">
                <button class="closeList" name="closeRequests" id="close-requests">Close</button>
            </div>
        </div>
    </nav>


    <nav id=helpTutorialId >
        <div class="flex-container" style="align-items:center;">
            <h1 class="titleDisplay">Video tutorial </h1>            

            <p class="message">Here you can find a video tutorial on how to use your options with Eurystheus</p>
            <div>
                <video src="tutorials/studentTutorial.mp4" controls poster="images/eu1.jpg"></video>
            </div>
            <div style="margin-left:auto;">
                <button class="closeList" name="closeTutorials" id="close-tutorials">Close</button>
            </div>
        </div>
    </nav>
    <nav id=helpPaperId >
        <div class="flex-container" id="helpTutorialIdFlexBox" style="align-items:center;">
            <h1 class="titleDisplay">Advices for  better report.</h1>            

            <p class="message">Here you can find some templates for the final report.<br>
            Special Thanks to the professor Panagiotis Vasiliadis,<br>for letting me use the links from his website.
            </p>
            <a href="reportTemplates/tempReport.docx">Report template</a>
            <a href="reportTemplates/tempReport1.docx">Advices from the professor P.Vasiliadis</a>
            <div style="margin-left:auto">
                <button class="closeList" name="closePaper" id="close-paper">Close</button>
            </div>
        </div>
    </nav>
    <nav id=helpPaperRecommendationId>
        <div class="flex-container">
            <h1 class="titleDisplay">Recomendations</h1>            
            
            <?php
            while($row = mysqli_fetch_assoc($recommendations)) {
                $recommendation=explode(',',$row ['recommendation_theme']);
                foreach ($recommendation as $rec) {
                    echo "<li id=liId style='font-size:25px'>".$rec."</li>";

                }
            }
            ?>    
            <div style="margin-left:auto;">
                <button class="closeList" name="closeRecommendation" id="close-recommendation" >Close</button>
            </div>
        </div>
    </nav>


    <h1 class="titleDisplay" id="welcomeTitleId" style="margin-right:auto;">Welcome<br> <?php echo $_SESSION['name'];?><br><?php if($_SESSION['verified']==1){
        echo "<span class='verifiedSpan'>Verified<i class='fas fa-check'></i></span>";}?>  </h1>  
             
    <?php if(count($errors)>0): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $error):?>
                <li><?php echo $error;?></li>
            <?php endforeach;?>
            <form action="index.php" method="post">
                <button name="OKerror" class="btn loginButton" id="OKerrorId" style="z-index:1;">OK</button>
            </form>
        </div>
    <?php endif;?>
    <?php if(isset($_SESSION['message'])):?>
        <div class="messageAlert">
            <li><?php echo $_SESSION['message'];?></li>
                <button name="OKmessage" class="btn loginButton" id='OKmessageID'  style="z-index:1;">OK</button>
        </div>
    <?php endif;?>

    <form action ="index.php" method="post" class="responsiveForm">
        <div >
            <button type="submit" name="seeDissertation" class="btn loginButton" id="indexPageBtn">see the current Dissertation</button>
        </div>
    </form>
    <form action ="recommendation.php" method="post" class="responsiveForm">
        <div >
            <button type="submit" name="recommendationSystem" class="btn loginButton" id="recommendationSystemId">Recommendation system</button>
        </div>
    </form>
    <form action="chart.php" method="post" class="responsiveForm">
        <button class="btn loginButton" id="chartId">charts</button>
    </form>
    <?php if($_SESSION['verified']==0): ?> 
        <div class="alert alert-danger" id="warningID" >
            You need to verify your account by clicking 
            the verification link we just e-meailed you at
            <strong><?php echo $_SESSION['email'];?></strong>
            <button name="OK" class="btn loginButton" id="OKId" style="z-index:1;">OK</button>
        </div>
    <?php endif;?>

    
    <form action ="index.php" method="post" style="margin-left:auto;">
        <div>
            <button type="submit" name="logoutButton" class="logout">logout</button>
        </div>
    </form>


    
    
    </div>
</body>
    <!--jquery import-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script type="text/javascript">  
        //responsive purposes
        heightOfNavs=$("nav").height();
        if(heightOfNavs<2*$(window).height()){
            $("nav").height(2*$(window).height()+100);
        }

        //display and close nav elements/animations etc
        var overlayList=document.getElementById('overlayList');
        document.getElementById('show-list').addEventListener('click',function(){
            overlayList.classList.add('show-menu');
        });
        document.getElementById('close-list').addEventListener('click',function(){
            overlayList.classList.remove('show-menu');
        });
        
       var overlayRequests=document.getElementById('overlayRequests');
        document.getElementById('show-requests').addEventListener('click',function(){
            overlayRequests.classList.add('show-menu');
        });
        document.getElementById('close-requests').addEventListener('click',function(){
            overlayRequests.classList.remove('show-menu');
        });

        var helpTutorial=document.getElementById('helpTutorialId');
        document.getElementById('show-tutorials').addEventListener('click',function(){
            helpTutorial.classList.add('show-menu');
        });
        document.getElementById('close-tutorials').addEventListener('click',function(){
            helpTutorial.classList.remove('show-menu');
        });


        var helpPaper=document.getElementById('helpPaperId');
        document.getElementById('show-paper').addEventListener('click',function(){
            helpPaper.classList.add('show-menu');
        });
        document.getElementById('close-paper').addEventListener('click',function(){
            helpPaper.classList.remove('show-menu');
        });

        var helpRecommendation=document.getElementById('helpPaperRecommendationId');
        document.getElementById('show-recommendation').addEventListener('click',function(){
            helpRecommendation.classList.add('show-menu');
        });
        document.getElementById('close-recommendation').addEventListener('click',function(){
            helpRecommendation.classList.remove('show-menu');
        });
        
       var table=document.getElementById('table');
       var requestDescriptionId=document.getElementById('requestDescriptionId');
       var requestThemeId=document.getElementById('requestThemeId');
       var mirrorTheme=''
       var mirrorDesc='';
       var mirrorReqs='';
       for(var i=1;i<table.rows.length;i++){
            table.rows[i].onclick=function(){

                tempHoldCellThemeValue=this.cells[1].innerHTML;
                mirrorTheme=tempHoldCellThemeValue.replace(/<br>/g,'');
                tempHoldCellThemeValue=mirrorTheme;

                tempHoldCellDescValue=this.cells[2].innerHTML;
                mirrorDesc=tempHoldCellDescValue.replace(/<br>/g,'');
                tempHoldCellDescValue=mirrorDesc;

                tempHoldCellReqValue=this.cells[3].innerHTML;
                mirrorReqs=tempHoldCellReqValue.replace(/<br>/g,'');
                tempHoldCellReqValue=mirrorReqs;

                document.getElementById('descriptionTextId').value=tempHoldCellDescValue;
                document.getElementById('themeFromList').value=tempHoldCellThemeValue;
                document.getElementById('requirementsTextId').value=tempHoldCellReqValue;

            }
        }

        var tableReq=document.getElementById('tableReq');
        var counterOfAccepted=0;
        for(var i=1;i<tableReq.rows.length;i++){
            resultOfRequest=tableReq.rows[i].cells[3].innerHTML;
            if(resultOfRequest=="Accepted"){
                counterOfAccepted+=1;
            }
        }
        if(counterOfAccepted>1){
            $('#showOnlyAcceptedRequestsID').show()    
        }else{
            $('#showOnlyAcceptedRequestsID').hide()
        }
        
        //readonly and required dont work together, so this is a 
        //'readonly' function for input and textarea
        $(".readonly").keydown(function(e){
            e.preventDefault();
        });
        
        //selected rows getting colored
        $('table tr').click(function () {
            $('table tr').each(function (a) {
                $(this).removeClass('selectedRow')
            });
            $(this).addClass('selectedRow');
        });
        
        //verification warning display:none
        var warningPanel=document.getElementById('warningID');
        var okBtn=document.getElementById('OKId');
        if(okBtn!=null){
            okBtn.onclick=function(){
            warningPanel.style.display="none";
            }
        }
        
        //z index of OK button.
        $('li').click(function () {
            if(warningPanel!=null){
                warningPanel.style.display="none";
            }
        });
        //responsive purposes
        $('ul li ul li').click(function(){
            setTimeout(function() {
                $('ul li').hide()
            }, 1000);
            $("button").click(function(){
                $("ul li").show();
            }); 
            
            
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
            url: 'index.php',
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
</html>
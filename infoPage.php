<?php


require_once 'controllers/authController.php';



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>Eurystheus</title>
    <link rel="shortcut icon" type="image/png" href="images/eu1.jpg">
    <link rel="stylesheet" href="cssFiles/infoPage.css">
    <link rel="stylesheet" href="cssFiles/aos.css">
    <link rel="stylesheet" href="cssFiles/buttonstyle.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="parallax.js-1.5.0/parallax.min.js"></script>
</head>
<body>       
        <div class="container">
            <div style="text-align:center">
                <h1 data-text="Welcome to Eurystheus UOI"  data-aos="zoom-in-up">Welcome to <span>Eurystheus</span>  UOI</h1>
                <div class="scrolldown">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div  data-aos="zoom-out-down">
                    <form action="infopage.php" method='post'>
                        <button  type="submit" name="redirectLogin" class="btn loginButtonInfo">Sign In</button>
                        <button  type="submit" name="redirectSignUp" class="btn loginButtonInfo" id=loginButtonInfoId>Sign Up</button>
                    </form>
                </div>
            </div>
            <div class="parallax" data-parallax="scroll" data-z-index="1" data-image-src="images/uoi2.jpg"></div>
            <div class="responsiveImage"><img src="images/eu1.jpg" style="width:100vw;"></div>
            <h3 data-aos="zoom-in">So,what is Eurystheus?</h3>
                <p data-aos="zoom-in-up" class="message">
                    Eurystheus is a web application that aims , to help professors and students about the
                    theme of dissertation.It was developed at University of Ioannina at the Computer science
                    department.Eurystheus is a web application that aims , to help professors and students about the
                    theme of dissertation.It was developed at University of Ioannina at the Computer science
                    department.Eurystheus is a web application that aims , to help professors and students about the
                    theme of dissertation.It was developed at University of Ioannina at the Computer science
                    department.
                </p>
                <div class="parallax" data-parallax="scroll" data-z-index="1" data-image-src="images/eu1.jpg"></div>
                <div class="responsiveImage"><img src="images/uoi2.jpg" style="width:100vw;"></div>
                <p data-aos="zoom-in-up" class="message">
                    Students can make requests for every single dissertation
                    theme they want with ease.They can find numerous ways to get help about the dissertation theme they
                    chose, such as examples from previous dissertations ,reports etc.
                    
                </p>
            <h3  data-aos="zoom-in">University of Ioannina</h3>
            <p data-aos="zoom-in-up" class="message">
                The university was founded in 1964, as a charter of the Aristotle University
                of Thessaloniki and became an independent university in 1970.The campus is located 6 km from the
                centre of Ioannina and is one of the largest university campuses in Greece.The buildings cover an
                area of 170,000 m2, consisting of lecture halls, offices, laboratories, libraries, amphitheaters, etc.
                Large classes are held in auditoriums, while scientific meetings and exhibitions are held in the Conference
                Centre located in the Medical Sciences complex.
            </p>
            <h3  data-aos="zoom-in">Department of CS and engineering</h3>
            <p data-aos="zoom-in-left" class="message">
            The Department of Computer Science and Engineering was founded in 1990.Initially it operated
            as a Department of Informatics while from June 2013 it evolved into a Department of Engineering,
            with a 5-year course leading to a Diploma. 
            </p>
            <div class="parallax" data-parallax="scroll" data-z-index="1" data-image-src="images/uoi3.jpg"></div>
            <div class="responsiveImage"><img src="images/uoi3.jpg" style="width:100vw;"></div>
            <h3 data-aos="zoom-in-down">Criteria for using this Eurystheus.</h3>
                <p data-aos="zoom-in-up" class="message">
                    Active CS UOI student at the 4th year(or more).
                </p>
            <h3 data-aos="zoom-in-left">Frequently asked questions.</h3>
                <p data-aos="zoom-in-up" class="message">
                    How you came up with this name ? 
                </p>
                <p data-aos="zoom-in-up" class="message">
                   -In greek mythology, Eurysteus was  the king of Tiryns, who was responsible to
                   impose the Twelve Labors on the legendary hero Heracles.<br>You can find more
                   informations here :<a href="https://en.wikipedia.org/wiki/Eurystheus#:~:text=In%20Greek%20mythology%2C%20Eurystheus%20(%2F,him%20as%20ruler%20of%20Argos.">Eurysteus</a><br>
                   Dissertation is consider to be the last "labour" for the student and this web application
                   aims to make the assignment easier.
                </p>
            <h3>About Us</h3>
            <div class="cardsContainer">
                <div class="card">
                    <div class="front">
                        <img src="images/uoi4.jpg" >
                    </div>
                    <div class="back">
                        <h3>UOI</h3>
                        <p><i class="fas fa-wifi"></i><span>Website</span><br><a href="https://www.uoi.gr/en/">uoi.gr</a></p>
                        <p><i class="fas fa-phone"></i></i><span>Call Center </span><br>(+30) 26510-07777</p>
                    </div>
                </div>
                <div class="card">
                    <div class="front">
                        <img src="images/uoi6.jpg" >
                    </div>
                    <div class="back">
                        <h3>CS UOI</h3>
                        <p><i class="fas fa-wifi"></i><span>Website</span><br><a href="https://www.cs.uoi.gr/">cs.uoi.gr</a></p>
                        <p><i class="fas fa-phone"></i></i><span>Phone number </span><br>26510-07458, 07213, 07196, 08817</p>
                        <p><i class="fas fa-envelope"></i><span>Email </span><br> gramcse@uoi.gr</p>
                    </div>
                </div>
                
            </div>
        </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.js"> </script>
    <script src="cssFiles/aos.js"> </script>
    <script type="text/javascript">
        AOS.init({
            duration:1500,
        });
    </script>

</body>
</html>
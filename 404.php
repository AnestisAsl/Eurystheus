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
    <title>404</title>
</head>
<body>
    <div class="flex-container" style="align-items: center;">
        <div class="preload">
            <h3>Loading</h3>
            <div class="loader">
                <div class="circle"></div>
                <div class="circle"></div>
            </div>
        </div>
        <h1 ><span id="NotFoundTitleId">404</span>  error</h1>
        <h1 >Page not Found</h1>
        <form action="login.php" method="post">
            <button class="btn loginButton">Back to Login</button>
        </form>
        <footer>
            <ul class="logoFooter">
                Eurysteus
            </ul>
            <ul><i class="fas fa-search-location "></i>Location
                <li>computer science UOI</li>
            </ul>
            <ul><i class="fas fa-file-signature"></i>Contact us
                <li>email : gramcse@uoi.gr</li>
                <li>phone number :26510-07458, 07213, 07196, 08817</li>
            </ul>    
        </footer>
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
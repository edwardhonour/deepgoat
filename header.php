<?php

function head() {
	echo ' <head> <meta charset="utf-8"> <meta http-equiv="X-UA-Compatible" content="IE=edge"> <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> <title>SQL Labs - Tools for Oracle and Angular Developers</title>';
	echo ' <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css"> <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"> <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"> ';
	echo ' <link rel="stylesheet" href="assets/fonts/font-awesome.min.css"> <link rel="stylesheet" href="assets/fonts/themify-icons.css"> <link rel="stylesheet" href="assets/owlcarousel/css/owl.carousel.css"> <link rel="stylesheet" href="assets/owlcarousel/css/owl.theme.css">';
        echo ' <link rel="stylesheet" href="assets/css/slicknav.css"><link rel="stylesheet" href="assets/css/magnific-popup.css"><link rel="stylesheet" href="assets/css/aos.css"><link rel="stylesheet" href="assets/css/style.css">';
header('Cache-Control: no-cache, must-revalidate');
?>
</head>
<?php
}

function h($i) {
echo ' <div id="navigation" class="fixed-top navbar-light bg-faded site-navigation"> <div class="container"> <div class="row"> <div class="col-lg-2 col-md-3 col-sm-4"> <div class="site-logo"> <a class="navbar-logo" href="index.html"><img src="assets/img/logo.png" alt=""></a>';
echo ' <a class="navbar-logo" href="index.html"><img src="assets/img/logo-two.png" class="logo-hidden" alt=""></a> </div> </div> <div class="col-lg-10 col-md-9 col-sm-8"> <div class="header_right"> <nav id="main-menu" class="ml-auto">';
echo '<ul>';
if ($i==1) { echo '<li><a style="color:yellow" href="index.php">Home</a></li>'; } else { echo '<li style="color:yellow"><a href="index.php">Home</a></li>'; }
if ($i==2) { echo '<li><a style="color:yellow" href="ng-plsql.php">PL/SQL Service</a></li>'; } else { echo ' <li><a href="ng-plsql.php">PL/SQL Service</a></li>'; }
if ($i==3) { echo '<li><a style="color:yellow" href="training.php">Training</a></li>'; } else { echo '<li><a href="training.php">Training</a></li>'; }
if ($i==4) { echo '<li><a style="color:yellow" href="support.php">Support</a></li>'; } else { echo '<li><a href="support.php">Support</a></li>'; }
if ($i==5) { echo '<li><a style="color:yellow" href="youtube.php">YouTube</a></li>'; } else { echo '<li><a href="youtube.php">YouTube</a></li>'; }
if ($i==6) { echo '<li><a style="color:yellow" href="repos.php">Repositories</a></li>'; } else { echo '<li><a href="repos.php">Repositories</a></li>'; }
if ($i==7) { echo '<li><a style="color:yellow" href="contact.php">Contact</a></li>'; } else { echo '<li><a href="contact.php">Contact</a></li>'; }
echo ' </ul> </nav> <div id="mobile_menu"></div> </div> </div> </div> </div> </div> ';
}
?>

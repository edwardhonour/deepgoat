<!DOCTYPE HTML>
<?php require('header.php'); ?>

<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Your Mail Has Been Send Successfully.</title>
			<!-- Latest Bootstrap min CSS -->
		<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">	
	<style>
		.welcome-area {
			position: relative;
			height: 700px
		}
		.welcome-area:before{
		background: rgba(0, 0, 0, 0.5);
			content: "";
			height: 100%;
			filter: alpha(opacity=80);
			position: absolute;
			width: 100%;
		}
		.hero-text{
		background: rgba(255,255,255,0.8);
		padding:60px;
		margin-top:200px;
		}
		.hero-text h1 {
		color: #35ac39;
		font-size: 60px;
		font-weight: 500;
		line-height: 70px;
		margin-bottom:60px;
		position: relative;
		text-transform: capitalize;
		}
		.back-home{
		background:#f54291;
		border: 2px solid #f54291;
		border-radius: 500px;
		color: #fff;
		font-weight: 600;
		position: relative;
		margin: 15px 15px 0;
		padding: 12px 36px;
		text-transform: uppercase;
		transition: all 0.2s ease 0s;
		}
		.back-home:hover,
		.back-home:focus {
			background: #7564e5;
			color: #fff;
			border: 2px solid #7564e5;
			text-decoration: none;
		}
	</style>
</head>

<body>
	<section class="welcome-area"  style="background-image: url(assets/img/bg/static-bg.jpg);  background-size:cover; background-position: center center;">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 col-sm-12 col-xs-12 text-center">
					<div class="hero-text">
						<h1>Your Mail Has Been Send Successfully.</h1>
						<a class="back-home" href="index.html">Back To Home</a>
					</div>
				</div>
			</div>
		</div>
	</section>
		
		<!-- Latest jQuery -->
		<script src="assets/js/jquery-1.12.4.min.js"></script>
		<!-- Latest compiled and minified Bootstrap -->
			<script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>

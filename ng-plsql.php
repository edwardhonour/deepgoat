<!DOCTYPE html>
<?php require('header.php'); ?>
<html lang="en">
<?php head(); ?>
		<div class="preloader">
			<div class="status">
				<div class="status-mes"></div>
			</div>
		</div>
                <?php h(2); ?>
		<section data-stellar-background-ratio="0.3" id="home" class="home_parallax ripple" style="background-image: url(assets/img/bg/me-header.jpg);  background-size:cover; background-position: center center;">			
			<div class="container">
				<div class="row">
				  <div class="col-lg-12 text-center">
					<div class="hero-text">
						<h1 style=" text-transform: uppercase;">ng-plsql-service</h1>
						<p>Use Oracle SQL, PL/SQL, and Stored Procedures and Functions directly in your Angular Modules.</p>
						<a href="#about" class="page-scroll btn btn-default btn-home-bg">youtube playlist</a>						
					</div>
				  </div><!--- END COL -->		  
				</div><!--- END ROW -->
			</div><!--- END CONTAINER -->
		</section>
		<!-- END  HOME DESIGN -->

		<!-- START ABOUT -->
		<section id="about" class="about_me section-padding">
		   <div class="container">
				<div class="row">					
					<div class="col-lg-6 col-sm-6 col-xs-12" data-aos="fade-up">
						<div class="single_about_img">
							<img src="assets/img/man.png" class="img-fluid" alt="" />
						</div>
					</div><!-- END COL -->					
					<div class="col-lg-6 col-sm-6 col-xs-12" data-aos="fade-up">
						<div class="single_about">
							<h1>Oracle SQL and PL/SQL in Angular</h1>
							<span></span>
							<p>The ability to use SQL and PL/SQL directy in Angular Components dramatically simplifies the development process and improves developer productivity. 
The NG-PLSQL-SERVICE library Module, available free on NPM contains a Data Service with functions for embedding SQL and PL/SQL, and a function for calling REST APIs. It also contains and easy to user Resolve for making REST API calls when pages 
are loaded using the Angular Router.</p>
							<p>The NG-PLSQL-SERVICE Module supports Apache, the APEX Listener (ORDS), Weblogic, and Tomcat using listener services available on GitHub.</p>
						</div>
					</div><!-- END COL -->				
				</div><!-- END ROW -->
			</div><!-- END CONTAINER -->
		</section>
		<!-- END ABOUT -->	
		
		<!-- START ABOUT -->
		<section class="about_us section-padding">
		   <div class="container">
				<div class="row">
					<div class="col-md-12 text-center">
						<div class="section-title">
							<h1>Learn More about NG-PLSQL-SERVICE</h1>
							<span></span>
							<p>Watch our Demo Videos and Read our Whitepapers</p>
						</div>
					</div>
				</div>
				<div class="row text-center">					
					<div class="col-lg-3 col-sm-6 col-xs-12" data-aos="fade-up">
						<div class="serviceBox">
							<div class="service-icon"><i class="fa fa-anchor"></i></div>
							<h3 class="title">Introduction Playlist</h3>
							<p class="description">Learn why calling SQL and PL/SQL along with Stored Procedures and Functions is critical for Oracle developers in an Angular Environment.</p>
						</div>
					</div><!-- END COL -->			
					<div class="col-lg-3 col-sm-6 col-xs-12" data-aos="fade-up">
						<div class="serviceBox">
							<div class="service-icon"><i class="fa fa-code"></i></div>
							<h3 class="title">NG-PLSQL-SERVICE Tutorial</h3>
							<p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin pharetra tortor.</p>
						</div>
					</div><!-- END COL -->			
					<div class="col-lg-3 col-sm-6 col-xs-12" data-aos="fade-up">
						<div class="serviceBox">
							<div class="service-icon"><i class="fa fa-building-o"></i></div>
							<h3 class="title">Webserver and Middleware Tutorial</h3>
							<p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin pharetra tortor.</p>
						</div>
					</div><!-- END COL -->			
					<div class="col-lg-3 col-sm-6 col-xs-12" data-aos="fade-up">
						<div class="serviceBox">
							<div class="service-icon"><i class="fa fa-briefcase"></i></div>
							<h3 class="title">Hybrid 3-Tier Architecture</h3>
							<p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sollicitudin pharetra tortor.</p>
						</div>
					</div><!-- END COL -->				
				</div><!-- END ROW -->
			</div><!-- END CONTAINER -->
		</section>
		<!-- END ABOUT -->	
			
		<!-- START PROMOTION  -->
		<div class="promotion_offer section-padding" style="background-image: url(assets/img/bg/promotion-bg.jpg); background-size:cover; background-position: center center;">
			<div class="container">
				<div class="row">
					<div class="col-lg-7 col-sm-12 col-xs-12" data-aos="fade-up">
						<div class="promotion_content">
							<p>To learn more you can read the documentation specifically designed for Oracle Developers.</p>
							<a href="#process" class="page-scroll btn btn-lg btn-promotion-bg">Read the Docs</a>	
						</div>
					</div><!-- END COL  -->
				</div><!--END  ROW  -->
			</div><!-- END CONTAINER  -->
		</div>
		<!-- END PROMOTION -->

		<!-- START CONTACT -->
		<section id="contact" class="contact_us section-padding">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="section-title text-center">
							<h1>Get in touch</h1>
							<span></span>
							<p>Lorem ipsum dolor sit amet consectetur adipisicing elitsed.</p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-sm-12 col-xs-12" data-aos="fade-up">
						<div class="contact_image">
							<img src="assets/img/contact.png" class="img-fluid" alt="" />
						</div>
					</div><!-- END COL  -->
					<div class="col-lg-6 col-sm-12 col-xs-12" data-aos="fade-up">
						<div class="contact">
							<form class="form" name="enq" method="post" action="contact.php" onsubmit="return validation();">
								<div class="row">
									<div class="form-group col-md-6">
										<input type="text" name="name" class="form-control" placeholder="Name" required="required">
									</div>
									<div class="form-group col-md-6">
										<input type="email" name="email" class="form-control" placeholder="Email" required="required">
									</div>
									<div class="form-group col-md-12">
										<input type="text" name="subject" class="form-control" placeholder="Subject" required="required">
									</div>
									<div class="form-group col-md-12">
										<textarea rows="6" name="message" class="form-control" placeholder="Your Message" required="required"></textarea>
									</div>
									<div class="col-md-12 text-center">
										<button type="submit" value="Send message" name="submit" id="submitButton" class="btn btn-lg btn-contact-bg" title="Submit Your Message!">Send Message</button>
									</div>
								</div>
							</form>
						</div>
					</div><!-- END COL  -->					
				</div><!-- END ROW -->
			</div><!-- END CONTAINER -->
		</section>
		<!-- END CONTACT -->
	
		<!-- FOOTER -->
		<div class="footer">
			<div class="container text-center">
				<div class="row">
					<div class="col-lg-12">
						<div class="footer_copyright">
							<p>&copy; 2022-2023 Tritanium Labs LLC. All Rights Reserved.</p>
						</div>
					</div>
				</div>
			</div><!--- END CONTAINER -->
		</div>
		<!-- END FOOTER -->		
		 
		<!-- Latest jQuery -->
			<script src="assets/js/jquery-1.12.4.min.js"></script>
		<!-- Latest compiled and minified Bootstrap -->
			<script src="assets/bootstrap/js/bootstrap.min.js"></script>
		<!-- modernizer JS -->		
			<script src="assets/js/modernizr-2.8.3.min.js"></script>																		
		<!-- owl-carousel min js  -->
			<script src="assets/owlcarousel/js/owl.carousel.min.js"></script>
		<!-- jquery nav -->
			<script src="assets/js/jquery.nav.js"></script>	
		<!-- jquery.slicknav -->
			<script src="assets/js/jquery.slicknav.js"></script>			
		<!-- magnific-popup js -->               
			<script src="assets/js/jquery.magnific-popup.min.js"></script>			
		<!-- jquery mixitup js -->   
			<script src="assets/js/jquery.mixitup.min.js"></script>	
		<!-- stellar js -->
			<script src="assets/js/jquery.stellar.min.js"></script>		
		<!-- scrolltopcontrol js -->
			<script src="assets/js/scrolltopcontrol.js"></script>									
		<!-- aos js -->
			<script src="assets/js/aos.js"></script>
		<!-- ripples js -->	
			<script src="assets/js/ripples-min.js"></script>		
		<!-- scripts js -->
			<script src="assets/js/scripts.js"></script>
    </body>
</html>

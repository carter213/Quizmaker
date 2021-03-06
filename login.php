<?php
session_start();

if(time()-$_SESSION('fail_time') > (5*60) ){
	unset($_SESSION('fail_time'));
	unset($_SESSION('fail_count'));
}


?>

<!DOCTYPE html>
<html lang="en">
<!-- Header
================================================== -->
<head>
<meta charset="utf-8">
<link href="./favicon.ico" rel="shortcut icon" />
<title>SuperCereal - Team 15</title>

<!-- Le styles -->
<link href="assets/css/bootstrap.css" rel="stylesheet">
<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
<link href="assets/css/docs.css" rel="stylesheet">
<link href="assets/js/google-code-prettify/prettify.css" rel="stylesheet">

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

<!-- Le fav and touch icons -->
<link rel="shortcut icon" href="assets/ico/favicon.ico">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">

</head>

<!-- Body
================================================== -->
<body data-spy="scroll" data-target=".bs-docs-sidebar">

<!-- Navbar
================================================== -->
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li><a href="./index">Home</a> </li>
         </ul>
      </div>
    </div>
  </div>
</div>


<!-- Login  -->
<section class="tab-content container">
  <section id="user" class="tab-pane active">
    <div class="row-fluid">
      <div class="span5 offset1">
        <form id="user_login_form" method ="post" action ="finish_login">
        	<fieldset class="form-group">	
        	  <legend class="form-group"> Login</legend>
	          <div class="form-group">
	            <label for="login_account">Account:</label>
	            <input type="text" class="form-control" 
	                   id="login_account" name="login_account" placeholder="Enter your account name" >
	          </div>
	          <div class="form-group">
	            <label for="login_password">Password:</label>
	            <input type="password" class="form-control"
	                   id="login_password" name="login_password" placeholder="Enter your password">
	          </div>
	          <?php

	          	$failcount = $_SESSION('fail_count');
	          	$failtime = $_SESSION('fail_time');
	          	//fail should count > 5 and fail time bigger than 5 mins
	          	if(!isset('fail_count') || ($failcount < 6 && ( time()-$failtime < (5*60) ) )){
	             print "<button type='submit' class='btn btn-primary'>Login</button> \n";
	         	}else{
	         	   print "<button type='submit' class='btn btn-primary' disabled>Login</button> \n";
	         	}
	         	print  "<a href='./getpassword'>Forgot password?</a>"
	          ?>
        	</fieldset>
        </form>
      </div>
      <div class="span4 offset1">
<!-- signup -->
        <form id="user_signup_form" method="post" action="signup.php">
        	<fieldset class="form-group">	
        	 <legend class="form-group"> Sign Up</legend>
	         <div class="form-group">
	            <label for="signup_account">New account:</label>
	            <input type="text" class="form-control" 
	                   id="signup_account" name="signup_account" placeholder="Enter your account name">
	          </div>
	          <div class="form-group">
	            <label for="signup_password">Password:</label>
	            <input type="password" class="form-control"
	                   id="signup_password" name="signup_password" placeholder="Enter new password">
	          </div>
	          <div class="form-group">
	            <label for="signup_confirm_password">Confirm Password:</label>
	            <input type="password" class="form-control"
	                   id="signup_confirm_password" name="signup_confirm_password" placeholder="Re-enter password">
	          </div>
	          <div class="form-group">
	            <label for="signup_email">Email:</label>

	            <input type="email" class="form-control"
	                   id="signup_email" name="signup_email" placeholder="Enter your E-mail">
	          </div>
	          <div class="form-group">
	            <label for="signup_role"> Role: </label>
	            <select class="form_control" id ="signup_role" name="signup_role">
	            	 <option value="Student">Student</option>
	            	 <option value="Instructor">Instructor</option>
	            	 <option value="Teaching Assistant">Teaching Assistant</option>
	            </select>
	          </div>
	          
	          <button type="submit" class="btn btn-primary">Sign Up</button>
        	</fieldset>
        	
        </form>
      </div>
    </div>
  </section>
</section>



<!-- Footer
================================================== -->
<footer class="footer">
  <div class="container">
    <p><a href="http://classes.pint.com/cse135/">CSE135 Homepage</a></p>
    <p>&copy; 2013 <a href="./index">SuperCereal</a>. All rights reserved.</p>
    <p class="pull-right"><a href="#">Back to top</a></p>
  </div>
</footer>

<!-- Le javascript
================================================== --> 
<!-- Scripting at bottom so page will load faster with knife out because everybody runs faster with their knife out pshhhffff --> 
<script type="text/javascript" async src="http://www.google-analytics.com/ga.js"></script><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script> 
<script src="assets/js/jquery.js"></script> 
<script src="assets/js/google-code-prettify/prettify.js"></script> 
<script src="assets/js/bootstrap-transition.js"></script> 
<script src="assets/js/bootstrap-alert.js"></script> 
<script src="assets/js/bootstrap-modal.js"></script> 
<script src="assets/js/bootstrap-dropdown.js"></script> 
<script src="assets/js/bootstrap-tab.js"></script> 
<script src="assets/js/bootstrap-tooltip.js"></script> 
<script src="assets/js/bootstrap-popover.js"></script> 
<script src="assets/js/bootstrap-button.js"></script> 
<script src="assets/js/bootstrap-collapse.js"></script> 
<script src="assets/js/bootstrap-carousel.js"></script> 
<script src="assets/js/bootstrap-typeahead.js"></script> 
<script src="assets/js/bootstrap-affix.js"></script> 
<script src="assets/js/application.js"></script>
</body>
</html>

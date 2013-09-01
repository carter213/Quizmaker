<!DOCTYPE html>
<html lang="en">
<!-- Header
================================================== -->
<head>
<meta charset="utf-8">
<link href="http://ucsd-cse-134.github.io/group18/Homework4/img/team_page/favicon.ico" rel="shortcut icon" />
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
      <a class="brand" href="./login">Log In</a>
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
    <div class="span4 offset4">
	    <form class="from-group" action="assign_new_password" method="post">
	    	<fieldset>
	    	<legend> Find Your Password </legend>
		     <div class="control-group">
				 <label class="control-label">Username</label>
				 <div class="controls">
					<div class="input-prepend">
						<input class="span15" id="username" type="text" name="username">
					</div>
				</div>
			     <label class="control-label">Email address</label>
			     <div class="controls">
			     	<div class="input-prepend">
			     		<span class="add-on"><i class="icon-envelope"></i></span>
			     		<input class="span15" id="email" type="email" name="email">
			     	</div>
			     </div>
			     <div class="controls">
			    	 <button type="submit" class="btn btn-primary"> Send </button>
			     </div>
			  </div>
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
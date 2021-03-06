<?php
session_start();

if (isset($_SESSION['role'])) {
	switch ($_SESSION['role']) {
		case 'Student':
			$management = 'studentmanagement';
			break;
		case 'Instructor':
			$management = 'profmanagement';
			break;
		case 'Teaching Assistant':
			$management = 'tamanagement';
			break;
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- Header
================================================== -->
<head>
<meta charset="utf-8">
<link href="favicon.ico" rel="icon" />
<title>Marketing Page - Super Cereal</title>

<!-- Le styles -->
<link href="assets/css/cssbundle.php?load=bootstrap,bootstrap-responsive,docs" rel="stylesheet">
<!--
<link href="assets/css/bootstrap.css" rel="stylesheet">
<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
<link href="assets/css/docs.css" rel="stylesheet"> -->
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

<!-- Local Styles -->
<style type="text/css">
p {
	color: #ECEDF0;
}
.thumbnail {
	border: 0px;
	padding-top: 15px;
	margin: 10px;
	min-height: 300px;
}
.bad {
	background-color: #e74c3c;
	border: solid 5px #FEEBEB;
}
.good {
	background-color: #008000;
	border: solid 5px #C1FFB9;
}
.caption {
	text-align: center;
}
</style>
</head>

<!-- Body
================================================== -->
<body data-spy="scroll" data-target=".bs-docs-sidebar">

<!-- Navbar
================================================== -->
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a class="brand" href="./homepage">Super Cereal</a>
      <?php if (!isset($_SESSION['user_id'])) { ?>
      <a class="brand" href="./login">Log In</a>
      <?php } else { ?>
      <a class="brand" href="./logout">Log Out</a>
      <?php } ?>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li class="active"> 
			<a href="./index">Home</a>
		  </li>
		  <?php
			if (isset($_SESSION['role'])) {
				print "<li><a href='${management}'>User Management</a></li>\n";
			}
			?>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Carousel
================================================== -->
<section id="carouselHeader">
  <div class="container">
    <div id="myCarousel" class="carousel slide">
      <div class="carousel-inner">
        <div class="item active"> <img src="img/makequiz.gif" alt="professor">
          <div class="carousel-caption">
            <h4>Want to make quizzes easily?</h4>
          </div>
        </div>
        <div class="item"> <img src="img/takequiz.gif" alt="student">
          <div class="carousel-caption">
            <h4>Want to take quizzes anywhere you want?</h4>
          </div>
        </div>
        <div class="item"> <img src="img/hands.gif" alt="hands">
          <div class="carousel-caption">
            <h4>Here we are!</h4>
            <p>For professors and students! We provide the easiest way to make and take quizzes!</p>
          </div>
        </div>
        <div class="item"> <img src="img/SuperCereal.gif" alt="super cereal">
          <div class="carousel-caption">
            <h4>Interest in us?</h4>
            <p>We are Group 15 - Super Cereal! Visit our member homepage <a href="./homepage">HERE</a>.</p>
          </div>
        </div>
        <div class="item"> <img src="img/cse135.gif" alt="Class Homepage">
          <div class="carousel-caption">
            <h4><a href="http://classes.pint.com/cse135/">CSE 135 Homepage</a></h4>
            <p>Taught us everything we know!</p>
          </div>
 
        </div>
      </div>
      <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a> <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a> </div>
  </div>
</section>

<!-- Content
================================================== -->
<section id="beforeAfter">
  <div class="container"> 
    
    <!-- Head -->
<!-- Welcome ! -->
    <section id="tableHeadWelcome">
      <div class="row-fluid">
        <div class="span16 offset0">
          <div class="thumbnail bad"> <img src="img/welcome.gif" alt="welcome" style="float: left; margin-left: 20px; margin-right: 50px;"/>
            <h1>Welcome!!!</h1>
            <p>&nbsp;</p>
            <p style="margin-right: 50px;">Quiz Maker is a web application designed to let you quickly and easily create and take
			   quizzes. Signup as a Professor, TA, or Student and use our convenient tools to fullfill your
			   every quiz related need! Professors can use the Quiz Maker application to design quizzes of any
			   length and style, and students can use Quiz Taker to take those quizzes in comfort! Finally,
			   Quiz Grader lets Professors and TAs grade those quizzes in the blink of an eye! Let Quiz Maker
			   help you today!</p>
          </div>
<!-- Feature ! -->
        <div class="span16">
          <div class="thumbnail good"> <img src="img/feature.gif" alt="feature" style="float: right; margin-left: 50px; margin-right: 20px;"/> 
            <div class="caption">
              <h1>Features:</h1>
            </div>
            <div  style="margin-left: 50px;">
              <p>Elegant drag/drop designs - easily format your quiz!</p>
              <p>All kinds of question templates - multiple choice, true/false, fill-in, etc. you can create your quiz on the fly! </p>
              <p>Quiz management - helps you record every quiz you've created or students have taken, and it's really easily to recover past quizzes.</p>
              <p>User management - professor, TA/graders, students, signup with different roles to easily manage your quizzes!</p>
           </div>
          </div>
        </div>
      </div>
      </div>
    </section>
    
<!-- Professors ! -->
    <section id="exampleComputerProblems">
      <div class="row-fluid">
        <div class="span16 offset0">
          <div class="thumbnail bad"> <img src="img/prof.gif" alt="Frustratedman" style="float: left; margin-left: 20px; margin-right: 50px;"/>
            <h1>For Professors:</h1>
            <p>&nbsp;</p>
            <p>You will not get buried by the stacks of papers!</p>
            <p>You will not struggle with formatting the quizzes!</p>
            <p>You can easily review and reuse previous quizzes!</p>
            <p>You can be a tree hugger - save papers and save time!</p>
          </div>
        </div>
<!-- TAs ! -->
        <div class="span16 offset0">
          <div class="thumbnail good"> <img src="img/grader.gif" alt="grade" style="float: right; margin-left: 50px; margin-right: 20px;"/>
            <div class="caption">
              <h1>For Graders/TAs:</h1>
            </div>
            <div style="margin-left: 80px;">
              <p>&nbsp;</p>
              <p>You don't have to carry boxes of papers!</p>
              <p>Let us help you grade!</p>
              <p>Easily write comments!</p>
              <p>Easily manage class grades!</p>
            </div>
          </div>
        </div>
      </div>
    </section>
<!-- Students ! -->
    <section id="tableHeadStudent">
      <div class="row-fluid">
        <div class="span16 offset0">
          <div class="thumbnail bad"> <img src="img/student.gif" alt="take" style="float: left; margin-left: 20px; margin-right: 50px;"/> 
            <h1>For Students:</h1>
            <p>&nbsp;</p>            
            <p>You can take quizzes anywhere and anytime!</p>
            <p>We'll help you manage your quizzes!</p>
            <p>Easily review your graded quizzes!</p>
            <p>Easily send your comments to the graders!</p>
          </div>
      </div>
      </div>
    </section>

  </div>
</section>

<!-- Footer
================================================== -->
<footer class="footer">
  <div class="container">
    <p><a href="http://classes.pint.com/cse135/">CSE135 Homepage</a></p>
    <p>&copy; 2013 <a href="./index">Super Cereal</a>. All rights reserved.</p>
    <p class="pull-right"><a href="#">Back to top</a></p>
  </div>
</footer>

<!-- Le javascript
================================================== --> 
<!-- Scripting at bottom so page will load faster with knife out because everybody runs faster with their knife out pshhhffff --> 
<script type="text/javascript" async src="http://www.google-analytics.com/ga.js"></script>
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<script type="text/javascript" src="assets/js/jsbundle.php?load=jquery,bootstrap-transition,bootstrap-alert,bootstrap-modal,bootstrap-dropdown,bootstrap-tab,bootstrap-tooltip,bootstrap-popover,bootstrap-button,bootstrap-collapse,bootstrap-carousel,bootstrap-typeahead,bootstrap-affix,application"></script> 
<script src="assets/js/google-code-prettify/prettify.js"></script> 

</body>
</html>

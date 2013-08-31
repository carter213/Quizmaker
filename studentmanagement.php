<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
  header('Location: /');
  exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
  header('Location: /');
  exit();
}

// Retrieve classes
$classes = mysqli_query($con, "SELECT * FROM class NATURAL JOIN class_member
  WHERE user_id=${user_id}");
  
// Retrieve quizzes
date_default_timezone_set('UTC');
$date = date("Y-m-d H:i:s");
$class_quiz_take_arrays = mysqli_query($con, "SELECT * FROM class NATURAL JOIN
  class_member NATURAL JOIN quiz WHERE user_id=${user_id} AND open_date<='${date}' 
  AND deadline>='${date}'");
  
$class_quiz_review_arrays = mysqli_query($con, "SELECT * FROM class NATURAL JOIN
  class_member NATURAL JOIN quiz NATURAL JOIN student_quiz WHERE user_id=${user_id} 
  AND graded=1");

?>

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
      <a class="brand" href="./logout">Logout</a>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li><a href="./index">Home</a> </li>
          <li class="active">
            <a href="./studentmanagement" class="nav_links">User Management</a>
          </li>
          <li><a href="./quiztaker" class="nav_links">Quiz Taker</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Content
================================================== -->
<section id="management_navbar" class="container">
  <ul class="nav nav-tabs">
  <li><a href="#user" data-toggle="tab">User</a></li>
  <li><a href="#classes" data-toggle="tab">Classes</a></li>
  </ul>
</section>

<!-- User settings -->
<section class="tab-content container">
  <section id="user" class="tab-pane active">
    <div class="row-fluid">
      <div class="span5 offset2">
        <form id="user_form" action="update_account" method="post">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" 
                   id="username" name="username" placeholder="Enter new username">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control"
                   id="email" name="email" placeholder="Enter new email">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" 
                   id="password" name="password" placeholder="Enter new password">
            <input type="password" class="form-control"
                   id="confirm_password" name="confirm_password" placeholder="Confirm Password">
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>
      <div class="span4 offset1">
        <form id="user_delete_form" action="delete_account" method="POST">
          <div class="form-group">
            <button type="button" onclick="confirm_delete()" class="btn btn-default">
              Delete Account</button>
            <button style="visibility:hidden" id="delete_btn" type="submit" 
             class="btn btn-default">Delete Account</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Class settings -->
  <section id="classes" class="tab-pane">
    <div class="row-fluid">
      <div class="span3 offset2">
        <form id="join_class_form" action="join_class" method="post">
          <div class="form-group">
            <label for="join_class">Join Class</label>
            <input type="text" class="form-control" 
                   id="join_class" name="join_class" placeholder="Class name">
            <input type="text" class="form-control"
                    id="join_class_code" name="join_class_code" placeholder="Class code">
          </div>
          <button type="submit" class="btn btn-default">Join class</button>
        </form>
      </div>
      <div class="span3">
        <form id="class_select_form" action="leave_class" method="post">
          <div class="form-group">
            <label for="classes_dropdown">Classes (changes Quizzes)</label>
            <select id="classes_dropdown" class="form-control" name="class_select"
                    onchange="change_class()">
              <?php
              while ($class = mysqli_fetch_array($classes)) {
                $class_code = $class['class_code'];
                $class_name = $class['class_name'];
                print "<option value='${class_code}'>${class_name}</option>";
              }
              ?>
            </select>
          </div>
          <button type="button" class="btn btn-default" 
                  onclick="confirm_leave_class()">Leave class</button>
          <button id="leave_class_btn" type="submit" style="display:none"
                  class="btn btn-default">Leave class</button>
        </form>
      </div>
      <div class="span3">
        <form id="quiz_take_select_form" action="./quiztaker">
          <div class="form-group">
            <label for="quizzes_take_dropdown">Open Quizzes</label>
            <select id="quizzes_take_dropdown" class="form-control" name="quiz_select">
              <option/>
              <?php
              while ($class_quiz = mysqli_fetch_array($class_quiz_take_arrays)) {
                $class_code = $class_quiz['class_code'];
                $quiz_name = $class_quiz['quiz_name'];
                print "<option class='${class_code}'>${quiz_name}</option>";
              }
              ?>
            </select>
            <input id="class_take_quiz" type="text" style="display:none" 
                   name="class_quiz"/>
          </div>
          <button type="submit" class="btn btn-default">Take</button>
        </form>
        <form id="quiz_review_select_form" action="./viewgrade">
          <div class="form-group">
            <label for="quizzes_review_dropdown">Graded Quizzes</label>
            <select id="quizzes_review_dropdown" class="form-control" name="quiz_select">
              <option/>
              <?php
              while ($class_quiz = mysqli_fetch_array($class_quiz_review_arrays)) {
                $class_code = $class_quiz['class_code'];
                $quiz_name = $class_quiz['quiz_name'];
                print "<option class='${class_code}'>${quiz_name}</option>";
              }
              ?>
            </select>
            <input id="class_review_quiz" type="text" style="display:none" 
                   name="class_quiz"/>
          </div>
          <button type="submit" class="btn btn-default">Review</button>
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

<script type="text/javascript">
  function confirm_delete() {
    var result = confirm("Are you sure you want to delete your account?");
    if (result == true) {
      document.getElementById("delete_btn").click();
    }
  }

  function change_class() {
    $("#class_take_quiz").val($("#classes_dropdown").val());
    $("#class_grade_quiz").val($("#classes_dropdown").val());

    $("#quizzes_take_dropdown").val('');
    $("#quizzes_review_dropdown").val('');

    var class_quiz_options = $("#quizzes_take_dropdown").children();
    $.each(class_quiz_options, function() {
      $(this).hide();
    });

    class_quiz_options = $("#quizzes_review_dropdown").children();
    $.each(class_quiz_options, function() {
      $(this).hide();
    });

    var shown_options = $("." + $("#classes_dropdown").val());
    $.each(shown_options, function() {
      $(this).show();
    });
  }

  function confirm_leave_class() {
    var result = confirm("Are you sure you want to leave this class?");
    if (result == true) {
      document.getElementById("leave_class_btn").click();
    }
  }
</script>
</body>
</html>
<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
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
$classes = mysqli_query($con, "SELECT * FROM class WHERE prof_id=${user_id}");

// Retrieve tas for each class
$class_ta_arrays = mysqli_query($con, "SELECT * FROM class NATURAL JOIN 
  class_member NATURAL JOIN user WHERE prof_id=${user_id} AND 
  role='Teaching Assistant'");

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
            <a href="./profmanagement" class="nav_links">User Management</a>
          </li>
          <li><a href="./quizmaker" class="nav_links">Quiz Maker</a></li>
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
            <button style="display:none" id="delete_btn" type="submit" 
             class="btn btn-default">Delete Account</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Class settings -->
  <section id="classes" class="tab-pane">
    <div class="row-fluid">
      <div class="span4 offset1">
        <form id="new_class_form" action="create_class" method="post">
          <div class="form-group">
            <label for="new_class">New Class</label>
            <input type="text" class="form-control" 
                   id="new_class" name="new_class" placeholder="New class name">
          </div>
          <button type="submit" class="btn btn-default">Create new class</button>
        </form>
        <br/>
        <form id="class_select_form" action="delete_class" method="post">
          <div class="form-group">
            <label for="classes_dropdown">Classes (changes TA and Quizzes)</label>
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
          <div class="form-group">
            Class Code (provide to students for sign-up): 
            <br/>
            <span id="class_code"></span>
          </div>
          <br/>
          <button type="button" onclick="confirm_delete_class()" 
                  class="btn btn-default">Delete class</button>
          <button id="delete_class_btn" style="display:none" type="submit" 
                  class="btn btn-default">Delete class</button>
        </form>
      </div>
      <div class="span3">
        <form id="new_ta_form" action="assign_ta" method="post">
          <div class="form-group">
            <label for="new_ta">New TA</label>
            <input type="text" class="form-control"
                   id="new_ta" name="new_ta" placeholder="New TA username">
            <input id="assign_ta_class" type="text" style="display:none"
                   name="class_code"> 
          </div>
          <button type="submit" class="btn btn-default">Assign new TA</button>
        </form>
        <br/>
        <form id="ta_select_form" action="remove_ta" method="post">
          <div class="form-group">
            <label for="ta_dropdown">TAs</label>
            <select id="ta_dropdown" class="form-control" name="ta_select">
              <option/>
              <?php
              while ($class_ta = mysqli_fetch_array($class_ta_arrays)) {
                $class_code = $class_ta['class_code'];
                $ta_name = $class_ta['account_name'];
                print "<option class='${class_code}'>${ta_name}</option>";
              }
              ?>
            </select>
            <input id="class_ta" type="text" style="display:none" 
                   name="class_ta"/>
          </div>
          <button type="button" class="btn btn-default" 
                  onclick="confirm_remove_ta()">Remove TA</button>
          <button id="remove_ta_btn" type="submit" style="display:none" 
                  class="btn btn-default">Remove TA</button>
        </form>
      </div>
      <div class="span3">
        <form id="quiz_select_form" action="./grading">
          <div class="form-group">
            <label for="quizzes_dropdown">Quizzes</label>
            <select id="quizzes_dropdown" class="form-control" name="quiz_select">
              <option>Quiz 1</option>
              <option>Quiz 2</option>
            </select>
          </div>
          <button type="submit" class="btn btn-default">Grade quiz</button>

            
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
    $("#class_code").html($("#classes_dropdown").val());
    $("#assign_ta_class").val($("#classes_dropdown").val());
    $("#class_ta").val($("#classes_dropdown").val());

    $("#ta_dropdown").val('');

    var class_ta_options = $("#ta_dropdown").children();
    $.each(class_ta_options, function() {
      $(this).hide();
    })

    var shown_options = $("." + $("#classes_dropdown").val());
    $.each(shown_options, function() {
      $(this).show();
    })
  }

  function confirm_delete_class() {
    var result = confirm("Are you sure you want to delete this class?");
    if (result == true) {
      document.getElementById("delete_class_btn").click();
    }
  }

  function confirm_remove_ta() {
    var result = confirm("Are you sure you want to remove this TA?");
    if (result == true) {
      document.getElementById("remove_ta_btn").click();
    }
  }

  change_class();
</script>
</body>
</html>

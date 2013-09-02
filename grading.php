<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || 
    ($_SESSION['role'] !== 'Instructor' && 
	   $_SESSION['role'] !== 'Teaching Assistant')) {
  header('Location: /');
  exit();
}

if (!isset($_GET['class_code']) || !isset($_GET['quiz_name'])) {
  header('Location: /');
  exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
  header('Location: /');
  exit();
}

$class_code = filter_var($_GET['class_code'], FILTER_SANITIZE_STRING);
$class_code = mysqli_real_escape_string($con, $class_code);
$quiz_name = filter_var($_GET['quiz_name'], FILTER_SANITIZE_STRING);
$quiz_name = mysqli_real_escape_string($con, $quiz_name);

// Check if class and quiz are valid
if (strlen($class_code) != 40 || strlen($quiz_name) == 0 || strlen($quiz_name) > 40) {
  if ($role == 'Instructor') {
    header('Location: profmanagement');
    exit();
  } else {
    header('Location: tamanagement');
	exit();
  }
}

if ($role == 'Instructor') {
  $valid_class = mysqli_query($con, "SELECT * FROM class NATURAL JOIN quiz WHERE 
    prof_id=${user_id} AND class_code='${class_code}' AND quiz_name='${quiz_name}'");

  if (mysqli_num_rows($valid_class) != 1) {
    header('Location: profmanagement');
    exit();
  }
} else {
  $valid_class = mysqli_query($con, "SELECT * FROM class_member NATURAL JOIN
    class NATURAL JOIN quiz WHERE user_id=${user_id} AND 
    class_code='${class_code}' AND quiz_name='${quiz_name}'");
	
  if (mysqli_num_rows($valid_class) != 1) {
    header('Location: tamanagement');
    exit();
  }
}

// Check if specific student requested
if (isset($_GET['student_name'])) {
  $load_student_name = $_GET['student_name'];

  $valid_quiz = mysqli_query($con, "SELECT * FROM quiz NATURAL JOIN class 
    NATURAL JOIN student_quiz NATURAL JOIN user WHERE 
    class_code='${class_code}' AND quiz_name='${quiz_name}' 
    AND account_name='${load_student_name}'");

  if (mysqli_num_rows($valid_quiz) == 1) {
    $valid_quiz = mysqli_fetch_array($valid_quiz);
    $load_quiz = 1;
    $load_quiz_id = $valid_quiz['quiz_id'];
    $student_id = $valid_quiz['user_id'];
  } else {
    $load_quiz = 0;
  }
} else {
  $load_quiz = 0;
}

// Get students
$students = mysqli_query($con, "SELECT * FROM quiz NATURAL JOIN class 
  NATURAL JOIN class_member NATURAL JOIN student_quiz NATURAL JOIN user WHERE
  class_code='${class_code}' AND quiz_name='${quiz_name}' AND finished=1");

?>

<!DOCTYPE html>
<html lang="en">
<!-- Header
================================================== -->
<head>
<meta charset="utf-8">
<title>Quiz Grading</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

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

<!-- Local Styles -->
<style type="text/css">
.qtitle {
	font-weight: bold;
}
legend + .qtitle:nth-of-type(1) {
	padding-top: 15px;
}
</style>
</head>

<!-- Body
================================================== -->
<body>

<!-- Navbar
================================================== -->
<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>

      <a class="brand" href="./logout">Logout</a>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li class=""> <a href="./index">Home</a> </li>
          <li class=""> 
            <?php
            if ($role == 'Instructor') {
              print '<a href="./profmanagement" class="nav_links">User Management</a>';
            } else {
              print '<a href="./tamanagement" class=nav_links">User Management</a>';
            }
            ?>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Content
================================================== -->
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span2"> </div>
    <div class="span8">
      <form action="submit_grading" method="post">
        <section>
          <select onchange="load_student_quiz()" name="loadStudentQuiz" id="loadStudentQuiz" class="span2">
            <?php
            while ($student = mysqli_fetch_array($students)) {
              $student_name = $student['account_name'];
              print "<option value='${student_name}' ";
              if ($load_quiz == 1) {
                if ($student_name === $load_student_name) {
                  print "selected";
                }
              }
              print ">${student_name}</option>";
            }
            ?>
          </select>
          <input id="quiz_name" type="text" style="display:none" name="quiz_name"
                 value=<?php print "'${quiz_name}'" ?>/>
          <input id="class_code" type="text" style="display:none" name="class_code" 
                 value=<?php print "'${class_code}'" ?>/>
		  <button type="button" class="btn btn-primary" onclick="load_student_quiz()" >Load Student Quiz</button>
        </section>
        <?php
        // Get questions
        if ($load_quiz == 1) {
          print "<section>";

          $questions = mysqli_query($con, "SELECT * FROM quiz NATURAL JOIN question
            NATURAL JOIN student_quiz NATURAL JOIN student_question 
            NATURAL JOIN user WHERE account_name='${load_student_name}' AND
            quiz_id=${load_quiz_id} ORDER BY question_num");

          while ($question = mysqli_fetch_array($questions)) {
            $label = $question['label'];
            $question_num = $question['question_num'];
            $body = $question['body'];
            $points = $question['points'];
            $student_points = $question['student_points'];
            $answer = $question['student_answer'];
            $ta_comment = $question['ta_comment'];
            $student_response = $question['student_response'];

            switch ($question['type']) {
              case 'mc':
                print "<div class='qtitle'>Question ${question_num}</div>\n";
                print "<span class='help-block'>${label}</span>\n";
                print "<div class='well well-small'>${body}\n";

                $options = mysqli_query($con, "SELECT * FROM mc
                  WHERE quiz_id='${load_quiz_id}' AND question_num='${question_num}' 
                  ORDER BY option_num");

                while ($option = mysqli_fetch_array($options)) {
                  $option_val = $option['option_val'];
                  $option_num = $option['option_num'];

                  $student_response = mysqli_query($con, "SELECT * FROM student_mc 
                    WHERE user_id=${student_id} AND quiz_id=${load_quiz_id} AND 
                    question_num=${question_num} AND option_num=${option_num}");

                  if (mysqli_num_rows($student_response) > 0) {
                    $checked = 1;
                  } else {
                    $checked = 0;
                  }

                  print "  <label class='radio'>\n";
                  print "    <input type='radio' ";
                  if ($checked == 1) {
                    print "checked ";
                  }
                  print "disabled>\n";
                  print "    ${option_val}\n";
                  print "  </label>\n";
                }

                print "</div>\n";
                print "<section class='form-horizontal'>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} Grade: </label>\n";
                print "  </div>\n";
                print "  <div class='input-append'>\n";
                print "    <select class='span10' name='points[]'>\n";

                for ($x = 0; $x < $points; $x++) {
                  print "      <option value='${x}' ";
                  if ($student_points == $x) {
                    print "selected";
                  }
                  print ">${x}</option>\n";
                }

                print "    </select>\n";
                print "    <span class='add-on'>/${points}</span>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} comment:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' name='ta_comment[]'>\n";
                print "      ${ta_comment}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Student response:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' disabled>\n";
                print "      ${student_response}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "</section>\n";

                break;

              case 'tf':
                print "<div class='qtitle'>Question ${question_num}</div>\n";
                print "<span class='help-block'>${label}</span>\n";
                print "<div class='well well-small'>${body}\n";
                print "  <label class='radio'>\n";
                print "    <input type='radio' ";
                if ($answer == 'True') {
                  print "checked ";
                }
                print "disabled>True</label>\n";
                print "  <label class='radio'>\n";
                print "    <input type='radio' ";
                if ($answer == 'False') {
                  print "checked ";
                }
                print "disabled>False</label>\n";
                print "</div>\n";
                print "<section class='form-horizontal'>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} Grade: </label>\n";
                print "  </div>\n";
                print "  <div class='input-append'>\n";
                print "    <select class='span10' name='points[]'>\n";

                for ($x = 0; $x < $points; $x++) {
                  print "      <option value='${x}' ";
                  if ($student_points == $x) {
                    print "selected";
                  }
                  print ">${x}</option>\n";
                }

                print "    </select>\n";
                print "    <span class='add-on'>/${points}</span>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} comment:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' name='ta_comment[]'>\n";
                print "      ${ta_comment}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Student response:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' disabled>\n";
                print "      ${student_response}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "</section>\n";

                break;

              case 'm':
                print "<div class='qtitle'>Question ${question_num}</div>\n";
                print "<span class='help-block'>${label}</span>\n";
                print "<div class='well well-small'>${body}\n";

                $options = mysqli_query($con, "SELECT * FROM student_matching 
                  NATURAL JOIN matching WHERE quiz_id=${load_quiz_id} AND 
                  user_id=${student_id} AND question_num=${question_num} 
                  ORDER BY option_num");

                while ($option = mysqli_fetch_array($options)) {
                  $word = $option['word'];
                  $value = $option['value'];
                  $answer = $option['answer'];

                  print "  <div class='row-fluid'>\n";
                  print "    <div class='span4'>${word}</div>\n";
                  print "    <div class='span6 offset2'>${value}</div>\n";
                  print "    <input type='text' class='span6' value='${answer}' disabled/>\n";
                  print "  </div>\n";
                }

                print "</div>\n";
                print "<section class='form-horizontal'>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} Grade: </label>\n";
                print "  </div>\n";
                print "  <div class='input-append'>\n";
                print "    <select class='span10' name='points[]'>\n";

                for ($x = 0; $x < $points; $x++) {
                  print "      <option value='${x}' ";
                  if ($student_points == $x) {
                    print "selected";
                  }
                  print ">${x}</option>\n";
                }

                print "    </select>\n";
                print "    <span class='add-on'>/${points}</span>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} comment:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' name='ta_comment[]'>\n";
                print "      ${ta_comment}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Student response:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' disabled>\n";
                print "      ${student_response}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "</section>\n";

                break;

              case 'fi':
                print "<div class='qtitle'>Question ${question_num}</div>\n";
                print "<span class='help-block'>${label}</span>\n";
                print "<div class='well well-small'>${body}\n";
                print "  <input type='text' value='${answer}' disabled>\n";
                print "</div>\n";
                print "<section class='form-horizontal'>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} Grade: </label>\n";
                print "  </div>\n";
                print "  <div class='input-append'>\n";
                print "    <select class='span10' name='points[]'>\n";

                for ($x = 0; $x < $points; $x++) {
                  print "      <option value='${x}' ";
                  if ($student_points == $x) {
                    print "selected";
                  }
                  print ">${x}</option>\n";
                }

                print "    </select>\n";
                print "    <span class='add-on'>/${points}</span>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} comment:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' name='ta_comment[]'>\n";
                print "      ${ta_comment}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Student response:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' disabled>\n";
                print "      ${student_response}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "</section>\n";

                break;
                
              case 'sa':
                print "<div class='qtitle'>Question ${question_num}</div>\n";
                print "<span class='help-block'>${label}</span>\n";
                print "<div class='well well-small'>${body}\n";
                print "  <br/>\n";
                print "  <textarea rows='5' cols='100' class='span6' readonly>${answer}</textarea>\n";
                print "</div>\n";
                print "<section class='form-horizontal'>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} Grade: </label>\n";
                print "  </div>\n";
                print "  <div class='input-append'>\n";
                print "    <select class='span10' name='points[]'>\n";

                for ($x = 0; $x < $points; $x++) {
                  print "      <option value='${x}' ";
                  if ($student_points == $x) {
                    print "selected";
                  }
                  print ">${x}</option>\n";
                }

                print "    </select>\n";
                print "    <span class='add-on'>/${points}</span>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Q${question_num} comment:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' name='ta_comment[]'>\n";
                print "      ${ta_comment}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <label>Student response:</label>\n";
                print "  </div>\n";
                print "  <div class='control-group'>\n";
                print "    <textarea rows='3' disabled>\n";
                print "      ${student_response}\n";
                print "    </textarea>\n";
                print "  </div>\n";
                print "</section>\n";

                break;

            }
          }

          print "</section>";
          print "<div class='form-actions'>\n";
          print "  <span class='pull-right'>\n";
          print "    <input id='save_submit' name='submit' type='number' value='0' style='display:none'>\n";
          print "    <button onclick='save_grading()' class='btn'>Save</button>\n";
          print "    <button onclick='submit_grading()' class='btn btn-success'>Submit</button>\n";
          print "    <button id='submit_btn' type='submit' style='display: none'></button>\n";
          print "  </span>\n";
          print "</div>\n";
        }
        ?>
      </form>
    </div>
  </div>
</div>

<!-- Footer
================================================== -->
<footer class="footer">
  <div class="container">
    <p><a href="http://classes.pint.com/cse134b/">CSE134B Homepage</a></p>
    <p>&copy; 2013 <a href="./index">The Four Amigos</a>. All rights reserved.</p>
    <p class="pull-right"><a href="#">Back to top</a></p>
  </div>
</footer>
<script type="text/javascript">
  function load_student_quiz() {
    var class_code = document.getElementById("class_code").value;
    var quiz_name = document.getElementById("quiz_name").value;
    var student_name = document.getElementById("loadStudentQuiz").value;

    window.location.href = "grading?class_code=" + class_code + "&quiz_name=" + quiz_name + "&student_name=" + student_name; 
  }

  function save_grading() {
    document.getElementById('save_submit').value = 0;
    document.getElementById('submit_btn').click();
  }

  function submit_grading() {
    document.getElementById('save_submit').value = 1;
    document.getElementById('submit_btn').click();
  }
</script>
</body>
</html>

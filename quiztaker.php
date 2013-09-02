<?php
session_start();

// Check user_id
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header('Location: /');
    exit();
}

//get class_code and quiz_name
if (!isset($_GET['class_code']) || !isset($_GET['quiz_name'])) {
    header('Location: /');
    exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
    header('Location: /');
    exit();
}


date_default_timezone_set('America/Los_Angeles');

$class_code = filter_var($_GET['class_code'], FILTER_SANITIZE_STRING);
$class_code = mysqli_real_escape_string($con, $class_code);
$quiz_name = filter_var($_GET['quiz_name'], FILTER_SANITIZE_STRING);
$quiz_name = mysqli_real_escape_string($con, $quiz_name);

//check if class_code is valid
if (strlen($class_code) != 40) {
    header('Location: studentmanagement');
    exit();
}

//check if quiz is valid
$class_id=mysqli_query($con, "SELECT class_id FROM class WHERE class_code = '${class_code}'");
$class_id=mysqli_fetch_array($class_id);
$class_id=$class_id['class_id'];
$quiz_found=mysqli_query($con, "SELECT quiz_id FROM quiz WHERE quiz_name = '${quiz_name}' AND class_id = '${class_id}'");

if (mysqli_num_rows($quiz_found) == 1) {
    $quiz_id = mysqli_fetch_array($quiz_found);
    $quiz_id=$quiz_id['quiz_id'];
} else {
   header('Location: studentmanagement');
   exit();
}

//check if the student is in class
$bool = False;
$members=mysqli_query($con, "SELECT user_id FROM class_member WHERE class_id = '${class_id}'");
while($member=mysqli_fetch_array($members)) {
    if($user_id == $member['user_id']) {
        $bool = True;
        break;
    }
}

//check if finished
$finished=mysqli_query($con, "SELECT finished FROM student_quiz WHERE user_id='${user_id}' AND quiz_id='${quiz_id}'");
$finished=mysqli_fetch_array($finished);
$finished=$finished['finished'];
if ($finished) {
    header('Location: studentmanagement');
    exit();
}

//check if time is valid 
//and for time limitation record current time
$date = date('Y-m-d H:i:s', time());
$open_date=mysqli_query($con, "SELECT open_date FROM quiz WHERE quiz_id='${quiz_id}'");
$open_date=mysqli_fetch_array($open_date);
$open_date=$open_date['open_date'];
$deadline=mysqli_query($con, "SELECT deadline FROM quiz WHERE quiz_id='${quiz_id}'"); 
$deadline=mysqli_fetch_array($deadline);
$deadline=$deadline['deadline'];
if ($bool == False || $date < $open_date || $date > $deadline) {
    header('Location: studentmanagement');
    exit();
}

//TODO: use time limitation
$time_limit=mysqli_query($con, "SELECT time_limit FROM quiz WHERE quiz_id='${quiz_id}'");
$time_limit=mysqli_fetch_array($time_limit);
$time_limit=$time_limit['time_limit'];
// Add $time_limit (total time) to start time. And store into session variable.
//if(!isset($_SESSION["start_time"])){$_SESSION["start_time"] = mktime(date(G),date(i),date(s),date(m),date(d),date(Y)) + ($time_limit * 60 + 1);} 
session_set_cookie_params($time_limit * 60);

?>


<!DOCTYPE html>
<html lang="en">
<!-- Header
================================================== -->
<head>
<meta charset="utf-8">
<link href="./favicon.ico" rel="shortcut icon" />
<title>Quiz Taker</title>
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
           <li><a href="./studentmanagement" class="nav_links">User Management</a> </li>
          <li class="active"><a class="nav_links">Quiz Taker</a></li>
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
      <form id="form" action="save_submit_quiz.php" method="post" enctype="multipart/form-data">
      <?php
        print "<input type='hidden' name='quiz_id' value='${quiz_id}'>";
        print "<input type='hidden' name='class_id' value='${class_id}'>";
        print "<fieldset>";

        //check order
        $display_order=mysqli_query($con, "SELECT display_order FROM quiz WHERE quiz_id='${quiz_id}'");
        $display_order=mysqli_fetch_array($display_order);
        $display_order=$display_order['display_order'];
        //check randomizeTaker option value
        if ($display_order=='Fixed Order') {
          //get questions sorted by question_num
          $sequence=mysqli_query($con, "SELECT * FROM question WHERE quiz_id = '${quiz_id}' ORDER BY question_num");
        } else if ($display_order=='Randomized Order') {
            //randomize
            $sequence=mysqli_query($con, "SELECT * FROM question WHERE quiz_id='${quiz_id}' ORDER BY RAND()");
            $temp_question_num=1;
        }
        while($question=mysqli_fetch_array($sequence)) {
          //retrieve question info
          $type = $question['type'];
          $body = $question['body'];
          $question_num = $question['question_num'];
          print "<input type='hidden' name='Q_Num[]' value='${question_num}'>";
          print "<input type='hidden' name='Q_Type[]' value='${type}'>";
          //print number
          if ($display_order=='Fixed Order') {
            print "<div class='qtitle'>Question ${question_num}</div>";
          }
          if ($display_order=='Randomized Order') {
            print "<div class='qtitle'>Question ${temp_question_num}</div>";
            $temp_question_num++;
          }

        //display questions
        switch ($type) {
          //true/false Qs
          case 'tf':
            print "<div class='well well-small'>${body}";
            print "<label class='radio'>";
            print "<input type='radio' name='${question_num}' id='${question_num}' value='True'>";
            print "True</label>";
            print "<label class='radio'>";
            print "<input type='radio' name='${question_num}' id='${question_num}' value='False'>";
            print "False</label>";
            print "</div>";
          break;
          //multiple Qs
          case 'mc':
            print "<span class='help-block'>Check all that apply</span>";
            print "<div class='well well-small'>${body}";
            $options=mysqli_query($con, "SELECT * FROM mc WHERE quiz_id = '${quiz_id}' AND question_num = '${question_num}' ORDER BY option_num");
            while($option=mysqli_fetch_array($options)) {
              $option_num = $option['option_num'];
              $option_val = $option['option_val'];
              //print options
              print "<label>";
              print "<input type='checkbox' name='${question_num}[]' id='${question_num}' value='${option_num}'>";
              print "&nbsp ${option_val}</label>";
            }
            print "</div>";
            break;
            //matching Qs
            case 'm':
              print "<span class='help-block'>Fill in blanks with the correct answer (copy and paste words/sentences)</span>";
              print "<div class='well well-small'> ${body}";
              //matching options
              $options=mysqli_query($con, "SELECT * FROM matching WHERE quiz_id = '${quiz_id}' AND question_num = '${question_num}' ORDER BY option_num");
              $values=mysqli_query($con, "SELECT value FROM matching WHERE quiz_id = '${quiz_id}' AND question_num = '${question_num}' ORDER BY RAND()");
              while($option=mysqli_fetch_array($options)) {
                $value=mysqli_fetch_array($values);
                $value=$value['value'];
                $option_num = $option['option_num'];
                $word = $option['word'];
                print "<div class='row-fluid'>";
                print "<div class='span4'>${option_num}. ${word}</div>";
                print "<div class='span6 offset2'>${value}</div>";
                print "<input type='text' class='span1' name='${question_num}[]' id='${question_num}'>";
                print "</div>";
              }
              print "</div>";
            break;
            //fill-in Qs
            case 'fi':
              print "<div class='well well-small'> ${body}<br>";
              print "<input type='text' placeholder='Fill in the Blank' name='${question_num}' id='${question_num}'>";
              print "</div>";
            break;
            //short answer Qs
            case 'sa':
              print "<div class='well well-small'> ${body}<br>";
              print "<textarea rows='5' cols='100' class='span6' name='${question_num}' id='${question_num}'></textarea>";
              print "</div>";
            break;
            default:
            }
          }
          print "</fieldset>";
      ?>


        <div class="form-actions"><span class="pull-right">
          <button class="btn btn-success" type="submit" name="status" value="Submit">Submit</button>
        </span></div>

      </form>
    </div>
  </div>
</div>

<!-- Footer
================================================== -->
<footer class="footer">
  <div class="container">
    <p><a href="http://classes.pint.com/cse135/">CSE135 Homepage</a></p>
    <p>&copy; 2013 <a href="./index">Super Cereal</a>. All rights reserved.</p>
    <p class="pull-right"><a href="#">Back to top</a></p>
  </div>
</footer>
</body>
</html>

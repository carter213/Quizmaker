<?php
session_start();

// Check session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Instructor') {
  header('Location: /');
  exit();
}

if (!isset($_GET['class_code'])) {
  header('Location: /');
  exit();
}

$user_id = $_SESSION['user_id'];

require '../dbaccess/connect.php';

if (mysqli_connect_errno($con)) {
  header('Location: /');
  exit();
}

$get_code = filter_var($_GET['class_code'], FILTER_SANITIZE_STRING);
$get_code = mysqli_real_escape_string($con, $get_code);

// Check if class code is valid
if (strlen($get_code) != 40) {
  header('Location: profmanagement');
  exit();
}

$valid_class = mysqli_query($con, "SELECT * FROM class WHERE prof_id=${user_id}
  AND class_code='${get_code}'");

if (mysqli_num_rows($valid_class) != 1) {
  header('Location: profmanagement');
  exit();
}

// Check if specific quiz requested
if (isset($_GET['quiz_name'])) {
  $load_quiz_name = $_GET['quiz_name'];

  $valid_quiz = mysqli_query($con, "SELECT * FROM quiz NATURAL JOIN class WHERE 
    prof_id=${user_id} AND class_code='${get_code}' AND 
    quiz_name='${load_quiz_name}'");

  if (mysqli_num_rows($valid_quiz) == 1) {
    $valid_quiz = mysqli_fetch_array($valid_quiz);
    $load_quiz = 1;
    $load_quiz_id = $valid_quiz['quiz_id'];
  } else {
    $load_quiz = 0;
  }
} else {
  $load_quiz = 0;
}

// Get quizzes
$quizzes = mysqli_query($con, "SELECT * FROM quiz NATURAL JOIN class WHERE
  class_code='${get_code}'");

?>

<!DOCTYPE html>
<html lang="en">
<!-- Header
================================================== -->
<head>
<meta charset="utf-8">
<link href="http://ucsd-cse-134.github.io/group18/Homework2/img/team_page/favicon.ico" rel="shortcut icon" />
<title>Quiz Maker</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Le styles -->
<link href="assets/css/bootstrap.css" rel="stylesheet">
<link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
<link href="assets/css/docs.css" rel="stylesheet">
<link href="assets/js/google-code-prettify/prettify.css" rel="stylesheet">
<link href="assets/css/custom-theme/jquery-ui-1.10.0.custom.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="assets/css/joyride-2.1.css">

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
<style type="text/css">
.well {
	min-height: 10px;
	padding: 0px;
	padding-left: 10px;
	padding-right: 10px;
	margin-bottom: 5px;
	background-color: #f5f5f5;
	border: 1px solid #e3e3e3;
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
      <a class="brand" href="./logout">Logout</a>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li> <a href="./index">Home</a> </li>
          <li><a href="./profmanagement" class="nav_links">User Management</a> </li>
          <li class="active"><a href="./quizmaker" class="nav_links">Quiz Maker</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Subhead
================================================== -->

 <form id="quizForm" action="saveQuiz" method="post" enctype="multipart/form-data">
<header class="jumbotron subhead" id="overview">
  <div class="container"> 
    
    <!-- Quiz Settings -->
   
      <fieldset id="quizSettings">
        <legend><i class="icon-wrench"></i> Quiz Settings <span class="pull-right">
        <button class="btn btn-warning" title="Settings Help" onclick="tutorialsettings(); return false;"><i class="icon-question-sign"></i> Quiz Settings Help</button>
        </span></legend>
        <div class="inline-block well">
          <label for="loadQuiz">Load Saved Quiz</label>
          <div class="input-append">
            <select name="loadQuizSelect" id="loadQuizSelect" class="span2">
              <option value="">--</option>
              <?php
              while ($quiz = mysqli_fetch_array($quizzes)) {
                $name = $quiz['quiz_name'];
                print "<option value='${name}' ";
                if ($load_quiz == 1) {
                  if ($name === $load_quiz_name) {
                    print "selected";
                  }
                }
                print ">${name}</option>";
              }
              ?>
            </select>
            <input type="text" name="class_code" style="display:none" 
                   value=<?php print "'${get_code}'" ?>/>
            <button class="btn btn-primary" id="loadQuiz" type="button" onclick="load_quiz()">Load</button>
          </div>
        </div>
        <div class="inline-block well">
          <label for="quizName">Quiz Name</label>
          <input type="text" name="quizName" id="quizName" placeholder="Quiz Name" class="span2"
          <?php
          if ($load_quiz == 1) {
            $quiz_name = $valid_quiz['quiz_name'];
            print "value='${quiz_name}'";
          }
          ?>
          >
        </div>
        <div class="inline-block well">
          <label for="timeLimit">Time Limit</label>
          <div class="input-append">
            <input name="timeLimit" class="input-small" id="timeLimit" type="number" min="1" step="1" placeholder="None"
            <?php
            if ($load_quiz == 1) {
              $time_limit = $valid_quiz['time_limit'];
              print "value='${time_limit}'";
            }
            ?>
            >
            <span class="add-on">minutes</span> </div>
        </div>
        <div class="inline-block well">
          <label for="possiblePoints">Possible Points</label>
          <input type="number" name="possiblePoints" class="input-small" min="1" id="possiblePoints" 
          <?php
          if ($load_quiz == 1) {
            $possible_points = $valid_quiz['possible_points'];
            print "value='${possible_points}'";
          } else {
            print "value='1'";
          }
          ?>
          >
        </div>
        <div class="inline-block well">
          <label for="viewAnswers">Reveal Answers</label>
          <select id="viewAnswers" name="viewAnswers" class="span2">
            <?php
            if ($load_quiz == 1) {
              $reveal_answers = $valid_quiz['reveal_answers'];
              print "<option value='Never' ";
              if ($reveal_answers == 'Never') {
                print "selected";
              }
              print ">Never</option>\n";

              print "<option value='After Deadline' ";
              if ($reveal_answers == 'After Deadline') {
                print "selected";
              }
              print ">After Deadline</option>\n";

              print "<option value='On Turn-in' ";
              if ($reveal_answers == 'On Turn-in') {
                print "selected";
              }
              print ">On Turn-in</option>\n";              
            } else { ?>
              <option value="Never" selected>Never</option>
              <option value="After Deadline">After Deadline</option>
              <option value="On Turn-in">On Turn-in</option>
            <?php
            }
            ?>
          </select>
        </div>
        <br />
        <div class="inline-block well">
          <label for="startDate">Quiz Opens</label>
          <div class="input-append">
            <input type="text" name="startDate" id="start_date" class="span2"  placeholder="YYYY-MM-DD"
            <?php
            if ($load_quiz == 1) {
              $open_date = substr($valid_quiz['open_date'], 0, 10);
              print "value='${open_date}'";
            }
            ?>
            >
            <input type="text" name="startTime" id="startTime" class="span2"   placeholder="HH:MM:SS 24 hours"
            <?php
            if ($load_quiz == 1) {
              $start_time = substr($valid_quiz['open_date'], 11, 8);
              print "value='${start_time}'";
            }
            ?>
            >
          </div>
          <span class="help-block"><small>The date & time the quiz opens to students</small></span> </div>
        <div class="inline-block well">
          <label for="endDate">Deadline</label>
          <div class="input-append">
            <input type="text" name="endDate" id="end_date" class="span2" placeholder="YYYY-MM-DD"
            <?php
            if ($load_quiz == 1) {
              $close_date = substr($valid_quiz['deadline'], 0, 10);
              print "value='${close_date}'";
            }
            ?>
            >
            <input type="text" name="endTime" id="endTime" class="span2"  placeholder="HH:MM:SS 24 hours"
            <?php
            if ($load_quiz == 1) {
              $close_time = substr($valid_quiz['deadline'], 11, 8);
              print "value='${close_time}'";
            }
            ?>
            >
          </div>
          <span class="help-block"><small>The date & time the quiz closes to students</small></span> </div>
        <div class="inline-block well">
          <label for="randomizeTaker">Display Questions in</label>
          <select id="randomizeTaker" name="randomizeTaker" class="span2">
            <?php
            if ($load_quiz == 1) {
              $order = $valid_quiz['display_order'];
              print "<option value='Fixed Order' ";
              if ($order == 'Fixed Order') {
                print "selected";
              }
              print ">Fixed Order</option>";

              print "<option value='Randomized Order' ";
              if ($order == 'Randomized Order') {
                print "selected";
              }
              print ">Randomized Order</option>";              
            } else { ?>
              <option value="Fixed Order" selected>Fixed Order</option>
              <option value="Randomized Order">Randomized Order</option>
            <?php
            }
            ?>
          </select>
          <span class="help-block">How to display questions to the students.</span> </div>
      </fieldset>
  </div>
</header>

<!-- Sidebar and Content
================================================== -->
<div class="container">
  <div class="row"> 
    
    <!-- Sidebar
    ================================================== -->
    <div class="span3 bs-docs-sidebar" id="amigoSidebar">
      <ol class="nav nav-list bs-docs-sidenav affix-top sortable">
        <li class="not-sortable"><a href="#"><i class="icon-chevron-up"></i> Back To Top</a></li>
        <li class="not-sortable">Points Allocated: <span id="totalPoints">0</span> / <span id="pointsPossible">1</span> </li>
        <li class="not-sortable" id="tourSortID"><a href="#" id="sorter"><i class="icon-random"></i> Reorder by Question Type</a></li>
        <li class="divider not-sortable" style="visibility:hidden"></li>
        <?php
        if ($load_quiz == 1) {
          $questions = mysqli_query($con, "SELECT * FROM question WHERE 
            quiz_id=${load_quiz_id} ORDER BY question_num");

          $num = mysqli_num_rows($questions);

          $i = 0;
          while ($question = mysqli_fetch_array($questions)) {
            $i++;
            print "<li data-linked='${i}' data-reset='1'>\n";
            print "  <span class='anchor'>\n";
            print "    <span class='qNum' style='display: inline'>${i}</span>. Question\n";
            print "    </span>";
            print "  </span>\n";
            print "</li>\n";
          }
        }

        ?>
      </ol>
    </div>
    
    <!-- Content
    ================================================== -->
    <div class="span9">
      <section id="contents"> 
        
        <!-- Add question -->
        <fieldset>
          <legend><i class="icon-plus"></i> Add Question <span class="pull-right">
          <button class="btn btn-warning" title="Question Creation Help" onclick="tutorialBase();"><i class="icon-question-sign"></i> Question Creation Help</button>
          </span> </legend>
          <input type="number" id="numToAdd" class="input-small" min="1" max="100" value="1" placeholder="# to add" />
          <select name="addQuestion" id="addQuestions">
            <option value="">Select Type</option>
            <option value="mc">Multiple Choice</option>
            <option value="tf">True / False</option>
            <option value="m">Matching</option>
            <option value="fi">Fill-in</option>
            <option value="sa">Short Answer</option>
          </select>
        </fieldset>
        
        <!-- Save Preview Clear -->
        <div class="form-actions">
          <input type="submit"  value="Save Quiz" class="btn btn-success" />
          <input type="button" value="Preview Quiz" class="btn" id="preview-bottom" onclick="preview()"/>
          <input type="reset" value="Start Over" class="btn btn-danger" id="clearAll-bottom" onclick="clearPage();"/>
        </div>
        
        <!-- New question inserted here -->
        <div id="questions">
          <?php
          if ($load_quiz == 1) {
            $questions = mysqli_query($con, "SELECT * FROM question WHERE 
              quiz_id=${load_quiz_id} ORDER BY question_num");

            while ($question = mysqli_fetch_array($questions)) {
              $label = $question['label'];
              $question_num = $question['question_num'];
              $body = $question['body'];
              $points = $question['points'];

              switch ($question['type']) {
                case 'mc':
                  $options = mysqli_query($con, "SELECT * FROM mc WHERE
                    quiz_id=${load_quiz_id} AND question_num=${question_num}
                    ORDER BY option_num");

                  print "<div class='newQuestion' data-type='mc' data-sort='${question_num}' id='${question_num}' style='opacity: 100; display: block;>\n";
                  print "  <span class='badge badge-info'>Multiple Choice</span>\n";
				  print "    <input type='text' style='display:none' name='questionNum[]' value='${question_num}'>\n";
				  print "    <input type='text' style='display:none' name='questionType[]' value='mc'>\n";
				  print "    <input type='text' style='display:none' name='questionID[]' value='${question_num}'>\n";
                  print "  <span class='pull-right'>\n";
                  print "    <div name='helpmcButton'>\n";
                  print "      <button class='btn btn-warning' title='Multiple Choice Help' onclick='tutorialmc()'>\n";
                  print "        <i class='icon-question-sign'></i>\n Multiple Choice Help\n";
                  print "      </button>\n";
                  print "    </div>\n";
                  print "  </span>\n";
                  print "  <a class='icon-trash close' href='#' style='color: red' name='deleteQ' title='Remove'></a>\n";
                  print "  <br>\n";
                  print "  <input type='text' name='questionName[]' value='${label}' placeholder='Question Label'>\n";
                  print "  <label>Question</label>\n";
                  print "  <textarea name='questionBody[]' class='textarea input-xxlarge'>${body}</textarea>\n";
                  print "  <span class='help-block'>\n";
                   print "  <span class='help-block'>\n";
                  print "    Type question in box above. Use underscores to indicate a 'blank', if applicable.\n";
                 print "  </span>\n \n";

                  print "  <label>Possible Answers</label>\n";
                  print "  <span class='help-block'>\n";
                  print "    <small>Check correct answer(s)</small>\n";
                  print "  </span>\n";
                  if (mysqli_num_rows($options) > 0) {
                    while ($option = mysqli_fetch_array($options)) {
                      $option_num = $option['option_num'];
                      $option_val = $option['option_val'];
                      $is_correct = $option['is_correct'];
                      print "  <div class='input-prepend input-append block'>\n";
                      print "    <span class='add-on'>\n";
                      print "      <input type='checkbox' value='${option_num}' name='${question_num}_mc_checked[]' ";
                      if ($is_correct) {
                        print "checked >\n";
                      } else { 
                        print "\>\n";
                      }
                      print "    </span>\n";
                      print "    <input type='text' name='${question_num}_mc_ans[]' value='${option_val}'/>\n";
                      print "    <i class='icon-trash btn btn-danger' title='Remove'></i>\n";
                      print "  </div>\n";
                    }
                  } else {
                    for ($x = 0; $x < 2; $x++) {
                      $option_num = $x;
                      print "  <div class='input-prepend input-append block'>\n";
                      print "    <span class='add-on'>\n";
                      print "      <input type='checkbox' value='${option_num}' name='${question_num}_mc_checked[]'/>\n";
                      print "    </span>\n";
                      print "    <input type='text' name='${question_num}_mc_ans[]'/>\n";
                      print "    <i class='icon-trash btn btn-danger' title='Remove'></i>\n";
                      print "  </div>\n";
                    }
                  }
                  print "  <div class='input-append'>\n";
                  print "    <input type='number' value='1' name='addAnswers' class='input-mini' min='1'>\n";
                  print "    <input type='button' class='btn btn-info' value='Add Answer(s)' name='addAnswer' data-type='mc'>\n";
                  print "  </div>\n";
                  print "  <label>Points</label>\n";
                  print "  <div name='PointBox'>\n";
                  print "    <input type='number' name='points[]' min='0' class='input-mini pointsBox' value='${points}'>\n";
                  print "  </div>\n";
                  print "</div>\n";

                  break;

                case 'tf':
                  $answer = $question['answer'];
                  print "<div class='newQuestion' data-type='tf' data-sort='${question_num}' id='${question_num}' style='opacity: 100; display: block;>\n";
                  print "  <span class='badge badge-info'>True/False</span>\n";
				  print "    <input type='text' style='display:none' name='questionNum[]' value='${question_num}'>\n";
				  print "    <input type='text' style='display:none' name='questionType[]' value='tf'>\n";
				  print "    <input type='text' style='display:none' name='questionID[]' value='${question_num}'>\n";
                  print "  <span class='pull-right'>\n";
                  print "    <div name='helptfButton'>\n";
                  print "      <button class='btn btn-warning' title='True/False Help' onclick='tutorialtf()'>\n";
                  print "        <i class='icon-question-sign'></i>\n True/False Help\n";
                  print "      </button>\n";
                  print "    </div>\n";
                  print "  </span>\n";
                 
                  print "  <a class='icon-trash close' href='#' style='color: red' name='deleteQ' title='Remove'></a>\n";
                  print "  <br>\n";
                  print "  <input type='text' name='questionName[]' value='${label}' placeholder='Question Label'>\n";
                  print "  <label>Question</label>\n";
                  print "  <textarea name='questionBody[]' class='textarea input-xxlarge'>${body}</textarea>\n";
                  print "  <span class='help-block'>\n";
                  print "    Type question in box above. Use underscores to indicate a 'blank', if applicable.\n";
                  print "  </span>\n";
                  print "  <label>Answers</label>\n";
                  print "  <label class='radio inline'>\n";
                  print "    <input type='radio' value='true' name='${question_num}_tf[]' ";
				  if ($answer == 'True') {
				    print "checked ";
				  }
				  print "/>True\n";
                  print "  </label>\n";
                  print "  <label class='radio inline'>\n";
                  print "    <input type='radio' value='false' name='${question_num}_tf[]' ";
				  if ($answer == 'False') {
					print "checked ";
				  }
				  print "/>False\n";
                  print "  </label>\n";
                  print "  <span class='help-block'>\n";
                  print "    <small>Select correct answer</small>\n";
                  print "  </span>\n";
                  print "  <label>Points</label>\n";
                  print "  <div name='PointBox'>\n";
                  print "    <input type='number' name='points[]' min='0' class='input-mini pointsBox' value='${points}'>\n";
                  print "  </div>\n";
                  print "</div>\n";

                  break;

                case 'm':
                  $options = mysqli_query($con, "SELECT * FROM matching WHERE 
                    quiz_id=${load_quiz_id} AND question_num=${question_num} 
                    ORDER BY option_num");

                  print "<div class='newQuestion' data-type='m' data-sort='${question_num}' id='${question_num}' style='opacity: 100; display: block;>\n";
                  print "  <span class='badge badge-info'>Matching</span>\n";
				  print "    <input type='text' style='display:none' name='questionNum[]' value='${question_num}'>\n";
				  print "    <input type='text' style='display:none' name='questionType[]' value='m'>\n";
				  print "    <input type='text' style='display:none' name='questionID[]' value='${question_num}'>\n";
                  print "  <span class='pull-right'>\n";
                  print "    <div name='helpmButton'>\n";
                  print "      <button class='btn btn-warning' title='Matching Help' onclick='tutorialm()'>\n";
                  print "        <i class='icon-question-sign'></i>\n Matching Help\n";
                  print "      </button>\n";
                  print "    </div>\n";
                  print "  </span>\n";

                  print "  <a class='icon-trash close' href='#' style='color: red' name='deleteQ' title='Remove'></a>\n";
                   print "  <br>\n";
                  print "  <input type='text' name='questionName[]' value='${label}' placeholder='Question Label'>\n";
				  print "  <textarea name='questionBody[]' class='textarea input-xxlarge' style='display:none' >${body}</textarea>\n";
                  print "  <label>Word-Value Pairs</label>\n";

                  if (mysqli_num_rows($options) > 0) {
                    while ($option = mysqli_fetch_array($options)) {
                      $option_num = $option['option_num'];
                      $word = $option['word'];
                      $value = $option['value'];

                      print "  <div class='block'>\n";
                      print "    <input type='text' name='${question_num}_m_word[]' value='${word}' placeholder='Word'/>\n";
                      print "    <div class='input-append'>\n";
                      print "      <input type='text' name='${question_num}_m_value[]' value='${value}' placeholder='Value'/>\n";
                      print "      <i class='icon-trash btn btn-danger' title='Remove'></i>\n";
                      print "    </div>\n";
                      print "  </div>\n";
                    }
                  } else {
                    for ($x = 0; $x < 2; $x++) {
                      print "  <div class='block'>\n";
                      print "    <input type='text' name='${question_num}_m_word[]' placeholder='Word'/>\n";
                      print "    <div class='input-append'>\n";
                      print "      <input type='text' name='${question_num}_m_value[]' placeholder='Value'/>\n";
                      print "      <i class='icon-trash btn btn-danger' title='Remove'></i>\n";
                      print "    </div>\n";
                      print "  </div>\n";
                    }
                  }
                  print "  <div class='input-append'>\n";
                  print "    <input type='number' value='1' name='addAnswers' class='input-mini' min='1'>\n";
                  print "    <input type='button' class='btn btn-info' value='Add Pair(s)' name='addAnswer' data-type='m'>\n";
                  print "  </div>\n";
                  print "  <label>Points</label>\n";
                  print "  <div name='PointBox'>\n";
                  print "    <input type='number' name='points[]' min='0' class='input-mini pointsBox' value='${points}'>\n";
                  print "  </div>\n";
                  print "</div>\n";

                  break;

                case 'fi':
                  $options = mysqli_query($con, "SELECT * FROM fill_in WHERE 
                    quiz_id=${load_quiz_id} AND question_num=${question_num} 
                    ORDER BY option_num");

                  print "<div class='newQuestion' data-type='fi' data-sort='${question_num}' id='${question_num}' style='opacity: 100; display: block;>\n";
                  print "  <span class='badge badge-info'>Fill-in</span>\n";
				  print "    <input type='text' style='display:none' name='questionNum[]' value='${question_num}'>\n";
				  print "    <input type='text' style='display:none' name='questionType[]' value='fi'>\n";
				  print "    <input type='text' style='display:none' name='questionID[]' value='${question_num}'>\n";
                  print "  <span class='pull-right'>\n";
                  print "    <div name='helpfiButton'>\n";
                  print "      <button class='btn btn-warning' title='Fill-in Help' onclick='tutorialfi()'>\n";
                  print "        <i class='icon-question-sign'></i>\n Fill-in Help\n";
                  print "      </button>\n";
                  print "    </div>\n";
                  print "  </span>\n";
                  print "  <a class='icon-trash close' href='#' style='color: red' name='deleteQ' title='Remove'></a>\n";
                  print "  <br>\n";
                  print "  <input type='text' name='questionName[]' value='${label}' placeholder='Question Label'>\n";
                  print "  <label>Question</label>\n";
                  print "  <textarea name='questionBody[]' class='textarea input-xxlarge'>${body}</textarea>\n";
                  print "  <span class='help-block'>\n";
                  print "    Type question in box above. Use underscores to indicate a 'blank', if applicable.\n";
                  print "  </span>\n";
                  print "  <label>Acceptable Answers</label>\n";

                  if (mysqli_num_rows($options) > 0) {
                    while ($option = mysqli_fetch_array($options)) {
                      $option_num = $option['option_num'];
                      $answer = $option['answer'];

                      print "  <div class='input-append block'>\n";
                      print "    <input type='text' value='${answer}' name='${question_num}_fi[]' class='input-block'/>\n";
                      print "    <i class='icon-trash btn btn-danger' title='Remove'></i>\n";
                      print "  </div>\n";
                    }
                  } else {
                    print "  <div class='input-append block'>\n";
                    print "    <input type='text' name='${question_num}_fi[]' class='input-block'/>\n";
                    print "    <i class='icon-trash btn btn-danger' title='Remove'></i>\n";
                    print "  </div>\n";
                  }

                  print "  <div class='input-append'>\n";
                  print "    <input type='number' value='1' name='addAnswers' class='input-mini' min='1'>\n";
                  print "    <input type='button' class='btn btn-info' value='Add Answer(s)' name='addAnswer' data-type='fi'>\n";
                  print "  </div>\n";
                  print "  <label>Points</label>\n";
                  print "  <div name='PointBox'>\n";
                  print "    <input type='number' name='points[]' min='0' class='input-mini pointsBox' value='${points}'>\n";
                  print "  </div>\n";
                  print "</div>\n";

                  break;

                case 'sa':
                  print "<div class='newQuestion' data-type='sa' data-sort='${question_num}' id='${question_num}' style='opacity: 100; display: block;>\n";
                  print "  <span class='badge badge-info'>Short Answer</span>\n";
				  print "    <input type='text' style='display:none' name='questionNum[]' value='${question_num}'>\n";
				  print "    <input type='text' style='display:none' name='questionType[]' value='sa'>\n";
				  print "    <input type='text' style='display:none' name='questionID[]' value='${question_num}'>\n";
                  print "  <span class='pull-right'>\n";
                  print "    <div name='helpsaButton'>\n";
                  print "      <button class='btn btn-warning' title='Short Answer Help' onclick='tutorialsa()'>\n";
                  print "        <i class='icon-question-sign'></i>\n Short Answer Help\n";
                  print "      </button>\n";
                  print "    </div>\n";
                  print "  </span>\n";
                  print "  <a class='icon-trash close' href='#' style='color: red' name='deleteQ' title='Remove'></a>\n";
                   print "  <br>\n";
                  print "  <input type='text' name='questionName[]' value='${label}' placeholder='Question Label'>\n";
                  print "  <label>Question</label>\n";
                  print "  <textarea name='questionBody[]' class='textarea input-xxlarge'>${body}</textarea>\n";
                  print "  <span class='help-block'>\n";
                  print "    Type question in box above. Use underscores to indicate a 'blank', if applicable.\n";
                  print "  </span>\n";
                  print "  <label>Points</label>\n";
                  print "  <div name='PointBox'>\n";
                  print "    <input type='number' name='points[]' min='0' class='input-mini pointsBox' value='${points}'>\n";
                  print "  </div>\n";
                  print "</div>\n";
                  break;

              }
            }
          }
          ?>
        </div>
      </section>
    </div>
  </div>
</div>
</form>
<!-- Footer
================================================== -->
<footer class="footer">
  <div class="container">
    <p><a href="http://classes.pint.com/cse134b/">CSE134B Homepage</a></p>
    <p>&copy; 2013 <a href="./index">The Four Amigos</a>. All rights reserved.</p>
    <p class="pull-right"><a href="#">Back to top</a></p>
  </div>
</footer>

<!-- Tour
================================================== -->
<section id="toolTipTour"> 
  
  <!-- Basic Tour -->
  <ol id="tour" style="display:none">
    <li data-id="numToAdd" data-options="tipLocation:top;tipAnimation:fade" data-button="10 Million!">
      <p>1. How many questions do you want to add? (1 is default)</p>
    </li>
    <li data-id="addQuestions" data-options="tipLocation:top" data-button="I did.">
      <p>2. Select the type of question you'd like to add.</p>
    </li>
    <li data-id="addQuestions" data-options="tipLocation:bottom" data-button="Okay.">
      <p>3. Now try another type of question. Add more than one if you haven't already!</p>
    </li>
    <li data-id="tourSortID" data-options="tipLocation:right;scroll:false;" data-button="Awesome!">
      <p>4. Now that you have multiple question types, you can group them into sections by hitting this button.</p>
    </li>
    <li data-id="amigoSidebar" data-options="tipLocation:bottom" data-button="Whoa!">
      <p>5. You can also drag the questions around in the sidebar to change their order.</p>
    </li>
    <li data-id="save-bottom" data-options="tipLocation:left" data-button="I'll remember.">
      <p>6. Dont forget to save your quiz before you leave! You can come back to it later.</p>
    </li>
    <li data-button="Let's do this!">
      <p>This concludes the first tutorial. You can select the help button on any created question for more tips about that type.</p>
    </li>
  </ol>
  
  <!-- Settings Tour -->
  <ol id="toursettings" style="display:none">
    <li data-id="loadQuizSelect" data-options="tipLocation:bottom;tipAnimation:fade" data-button="Next">
      <p>Select a previously saved quiz here.</p>
    </li>
    <li data-id="quizName" data-options="tipLocation:bottom" data-button="Next">
      <p>Name this quiz.</p>
    </li>
    <li data-id="timeLimit" data-options="tipLocation:bottom" data-button="Next">
      <p>Leave blank for unlimited time.</p>
    </li>
    <li data-id="possiblePoints" data-options="tipLocation:bottom" data-button="Next">
      <p>The total points that this quiz is worth relative to students' final grade.</p>
    </li>
    <li data-id="viewAnswers" data-options="tipLocation:bottom" data-button="Next">
      <p>When can the students see the answer key?</p>
    </li>
    <li data-id="startDate" data-options="tipLocation:bottom" data-button="Next">
      <p>When will the quiz be available?</p>
    </li>
    <li data-id="endDate" data-options="tipLocation:bottom" data-button="Next">
      <p>When is the quiz due?</p>
    </li>
    <li data-id="randomizeTaker" data-options="tipLocation:bottom" data-button="Next">
      <p>In what order should the questions be presented to students?</p>
    </li>
    <li data-button="I'm ready!">
      <p>This completes the quiz settings tutorial. Next, start adding questions. For more help, click the orange Question Creation Help button in the Add Question section of this page.</p>
    </li>
  </ol>
  
  <!-- True/False -->
  <ol id="tf" style="display:none">
    <li data-button="Great!">
      <p>True/False:</p>
      <ol>
        <li>(Optional) Name the question. Not visible to taker.</li>
        <li>Type your question.</li>
        <li>(Optional) Select Graphic.</li>
        <li>Select correct answer. (True or False)</li>
        <li>Choose how many points the question is worth.</li>
      </ol>
    </li>
  </ol>
  
  <!-- Multiple Choice -->
  <ol id="mc" style="display:none">
    <li data-options="tipLocation:top;tipAnimation:fade" data-button="Great!">
      <p>Multiple Choice:</p>
      <ol>
        <li>(Optional) Name the question. Not visible to taker.</li>
        <li>Type your question.</li>
        <li>(Optional) Select Graphic.</li>
        <li>Choose either one or more than one correct answers.</li>
        <li>Fill in answers and select the correct one.</li>
        <li>(Optional) Add more answers.</li>
        <li>Choose how many points the question is worth.</li>
      </ol>
    </li>
  </ol>
  
  <!-- Short Answer -->
  <ol id="sa" style="display:none">
    <li data-options="tipLocation:top;tipAnimation:fade" data-button="Great!">
      <p>Short Answer:</p>
      <ol>
        <li>(Optional) Name the question. Not visible to taker.</li>
        <li>Type your question.</li>
        <li>(Optional) Select Graphic.</li>
        <li>Choose how many points the question is worth.</li>
        <li>This type of question must be manually graded.</li>
      </ol>
    </li>
  </ol>
  
  <!-- Fill In -->
  <ol id="fi" style="display:none">
    <li data-options="tipLocation:top;tipAnimation:fade" data-button="Great!">
      <p>Fill-In:</p>
      <ol>
        <li>(Optional) Name the question. Not visible to taker.</li>
        <li>Type your question, using underscores ("_____") to indicate a blank.</li>
        <li>(Optional) Select Graphic.</li>
        <li>Fill in possible correct answers.</li>
        <li>(Optional) Add more answers.</li>
        <li>Choose how many points the question is worth.</li>
      </ol>
    </li>
  </ol>
  
  <!-- Matching -->
  <ol id="m" style="display:none">
    <li data-options="tipLocation:top;tipAnimation:fade" data-button="Great!">
      <p>Matching:</p>
      <ol>
        <li>(Optional) Name the question. Not visible to taker.</li>
        <li>(Optional) Select Graphic.</li>
        <li>Type primary word or phrase on left, with the matching value on the right. The right column will be randomized for the test taker.</li>
        <li>(Optional) Add more answers.</li>
        <li>Choose how many points the question is worth.</li>
      </ol>
    </li>
  </ol>
</section>

<!-- Le javascript
================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="assets/js/jquery-1.10.1.js"></script> 
<script src="assets/js/google-code-prettify/prettify.js"></script> 
<script src="assets/js/bootstrap-transition.js"></script> 
<script src="assets/js/bootstrap-alert.js"></script> 
<script src="assets/js/bootstrap-modal.js"></script> 
<script src="assets/js/bootstrap-dropdown.js"></script> 
<script src="assets/js/bootstrap-scrollspy.js"></script> 
<script src="assets/js/bootstrap-tab.js"></script> 
<script src="assets/js/bootstrap-tooltip.js"></script> 
<script src="assets/js/bootstrap-popover.js"></script> 
<script src="assets/js/bootstrap-button.js"></script> 
<script src="assets/js/bootstrap-collapse.js"></script> 
<script src="assets/js/bootstrap-carousel.js"></script> 
<script src="assets/js/bootstrap-typeahead.js"></script> 
<script src="assets/js/bootstrap-affix.js"></script> 
<script src="assets/js/jasny-bootstrap.min.js"></script> 
<script src="assets/js/application.js"></script> 
<script src="assets/js/jquery-ui-1.10.3.custom.js"></script> 
<script src="assets/js/jquery.cookie.js"></script> 
<script src="assets/js/modernizr.mq.js"></script> 
<script src="assets/js/jquery.joyride-2.1.js"></script> 
<script src="assets/js/jquery.mockjax.js"></script> 
<script src="assets/js/date.format.js"></script> 
<script type="text/javascript">


function load_quiz() {
	var class_code = <?php print "'${get_code}'" ?>;
	var quiz_name = document.getElementById("loadQuizSelect").value;
	
	window.location.href = "quizmaker?class_code=" + class_code + "&quiz_name=" + quiz_name;
}

<?php
if ($load_quiz == 1) {
	print "numQuestions = ${num};\n";
}
?>
</script>
</body>
</html>

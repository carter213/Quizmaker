<!DOCTYPE html>
<html lang="en">
<!-- Header
================================================== -->
<head>
<meta charset="utf-8">
<link href="http://ucsd-cse-134.github.io/group18/Homework2/img/team_page/favicon.ico" rel="shortcut icon" />
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
      <a class="brand" href="./index">Logout</a>
      <div class="nav-collapse collapse">
        <ul class="nav">
          <li class=""> <a href="./index">Home</a> </li>
           <li class=""> <a href="./studentmanagement" class="nav_links">User Management</a> </li>
           <li><a href="./quiztaker" class="nav_links">Quiz Taker</a></li>
          
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
      <section>
        <fieldset>
          <legend>True / False</legend>
          <div class="qtitle">Question 1</div>
          <div class="well well-small">A browser is the same as a search engine.
            <label class="radio">
              <input type="radio" name="optionsRadios1" id="optionsRadiosQ1T" value="option1" checked>
              True</label>
            <label class="radio">
              <input type="radio" name="optionsRadios1" id="optionsRadiosQ1F" value="option2" disabled>
              False</label>
          </div>
           <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q1 Grade: 0/5 </label>
	      	</div>
	      	<div class="control-group">
	      		<label> Answer: </label>
	      			<p class="text-error"> Browser is a software application for retrieving, 
	      			presenting and traversing information resources on the World Wide Web.</p>
	      		
	      	</div>
	      	
	       <div class="control-group" >
	       		<label>Q1 comment:</label>
		       		<p class="muted"> none</p>
	       	</div>
          </form>        
            <div class="qtitle">Question 2</div>
          <div class="well well-small">The median is the value that occurs most often in a sample of data.
            <label class="radio">
              <input type="radio" name="optionsRadios2" id="optionsRadiosQ2T" value="1" disabled>
              True</label>
            <label class="radio">
              <input type="radio" name="optionsRadios2" id="optionsRadiosQ2F" value="0" checked>
              False</label>       
              
          </div>
           <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q2 Grade: 5/5 </label>
	      	</div>
	      	
	       <div class="control-group" >
	       		<label>Q2 comment:</label>
	       		<p class="muted"> Great! </p>
	       	</div>
          </form>            
          <div class="qtitle">Question 3</div>
          <div class="well well-small">If two nonempty sets are independent, they can not be disjoint.
            <label class="radio">
              <input type="radio" name="optionsRadios3" id="optionsRadiosQ3T" value="1" checked>
              True</label>
            <label class="radio">
              <input type="radio" name="optionsRadios3" id="optionsRadiosQ3F" value="0" disabled>
              False</label>
          </div>
          <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q3 Grade: 5/5 </label>
	      	</div>
	      	
	       <div class="control-group" >
	       		<label>Q3 comment:</label>
	       		<p class="muted"> Great! </p>
	       	</div>
          </form>  
         
        </fieldset>
        <fieldset>
          <legend>Multiple Choice</legend>
          <div class="qtitle">Question 4</div>
          <span class="help-block">Select only one option</span>
          <div class="well well-small">What causes night and day?
            <label class="radio">
              <input type="radio" name="optionsRadios4" id="optionsRadiosQ4A" value="A" disabled>
              A. The earth spins on its axis.</label>
            <label class="radio">
              <input type="radio" name="optionsRadios4" id="optionsRadiosQ4B" value="B" checked>
              B. The earth moves around the sun.</label>
            <label class="radio">
              <input type="radio" name="optionsRadios4" id="optionsRadiosQ4C" value="C" disabled>
              C. Clouds block out the sun's light.</label>
            <label class="radio">
              <input type="radio" name="optionsRadios4" id="optionsRadiosQ4D" value="D" disabled>
              D. The earth moves into and out of the sun's shadow.</label>
            <label class="radio">
              <input type="radio" name="optionsRadios4" id="optionsRadiosQ4E" value="E" disabled>
              E. The sun goes around the earth.</label>
          </div>
           <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q4 Grade: 5/5 </label>
	      	</div>
	      	
	       <div class="control-group" >
	       		<label>Q4 comment:</label>
	       		<p class="muted"> Great! </p>
	       	</div>
          </form>  
          <div class="qtitle">Question 5</div>
          <span class="help-block">Check all that apply</span>
          <div class="well wells-small">Which colors do you like?
            <label>
              <input type ="checkbox" name="Q5" id="Q5A" disabled>
              A. Red</label>
            <label>
              <input type ="checkbox" name="Q5" id="Q5B" checked disabled>
              B. Blue</label>
            <label>
              <input type ="checkbox" name="Q5" id="Q5C" disabled>
              C. Green</label>
            <label>
              <input type ="checkbox" name="Q5" id="Q5D" disabled>
              D. Yellow</label>
            <label>
              <input type ="checkbox" name="Q5" id="Q5E" disabled>
              E. Purple</label>
            <label>
              <input type ="checkbox" name="Q5" id="Q5F" disabled>
              F. All of the Above</label>
          </div>
        </fieldset>
          <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q5 Grade: 5/5 </label>
	      	</div>
	      	
	       <div class="control-group" >
	       		<label>Q5 comment:</label>
	       		<p class="muted"> Great! </p>
	       	</div>
          </form>  
        <fieldset>
          <legend>Matching</legend>
          <div class="qtitle">Question 6</div>
          <span class="help-block">Fill in blanks with corresponding letters</span>
          <div class="well well-small"> Match each quotation with the appropriate play
            <div class="row-fluid">
              <div class="span4">1. ____ The Tempest</div>
              <div class="span6 offset2">A. Small to greater matters must give way.</div>
              <input type="text" class="span1" disabled value="D">
            </div>
            <div class="row-fluid">
              <div class="span4">2. ____ King John</div>
              <div class="span6 offset2">B. For I am nothing, if not critical</div>
              <input type="text" class="span1" value="B" disabled>
            </div>
            <div class="row-fluid">
              <div class="span4">3. ____ Othello</div>
              <div class="span6 offset2">C. I would fain die a dry death.</div>
              <input type="text" class="span1" disabled value= "C">
            </div>
            <div class="row-fluid">
              <div class="span4">4. ____ Anthony and Cleopatra</div>
              <div class="span6 offset2">D. Sweet, sweet, sweet poison for the age's tooth.</div>
              <input type="text" class="span1" disabled value="A">
            </div>
          </div>
        </fieldset>
        
           <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q6 Grade: 5/5 </label>
	      	</div>
	      	
	       <div class="control-group">
	       		<label>Q6 comment:</label>
	       		<p class="muted"> Great! </p>
	       </div>
          </form>  
        
        
        
        
        
        <fieldset>
          <legend>Fill-in</legend>
          <div class="qtitle">Question 7</div>
          <div class="well well-small"> Lorem ipsum dolor sit amet, ____________________ adipiscing elit. <br>
            <input type="text" value= "Not IDEA" disabled>
          </div>
            <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q7 Grade: 5/5 </label>
	      	</div>
	      	
	       <div class="control-group" >
	       		<label>Q7 comment:</label>
	       		<p class="muted"> Great! </p>
	       	</div>
          </form>  
          
          
          
          <div class="qtitle">Question 8</div>
          <div class="well well-small"> My favorite class is ____________________ . <br>
            <input type="text" value="CSE135" disabled>
          </div>
            <form class="form-horizontal">
	      	 <div class="control-group" >
	      	 	<label>Q8 Grade: 5/5 </label>
	      	 </div>
	      	
	         <div class="control-group">
	       		<label>Q8 comment:</label>
	       		<p class="muted"> Great! </p>
	       	 </div>
            </form>  
          <div class="qtitle">Question 9</div>
          <div class="well well-small">20 + 3( __ - 1) = 32
            <label class="radio">
              <input type="radio" name="optionsRadios9" id="optionsRadiosQ9A" value="A" disabled>
              A. 8</label>
            <label class="radio">
              <input type="radio" name="optionsRadios9" id="optionsRadiosQ9B" value="B" checked>
              B. 5</label>
            <label class="radio">
              <input type="radio" name="optionsRadios9" id="optionsRadiosQ9C" value="C" disabled>
              C. 10</label>
            <label class="radio">
              <input type="radio" name="optionsRadios9" id="optionsRadiosQ9D" value="D" disabled>
              D. 7</label>
          </div>
        </fieldset>
        <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q9 Grade: 5/5 </label>
	      	</div>
	      	
	       <div class="control-group" >
	       		<label>Q9 comment:</label>
	       		<p class="muted"> Great! </p>	      
	       </div>
          </form>  
        <fieldset>
          <legend>Short Answer</legend>
          <div class="qtitle">Question 10</div>
          <div class="well well-small">Why should we learn HTML5? <br>
            <textarea rows="5" cols="100" class="span6" readonly>I don't know</textarea>
          </div>
        </fieldset>
          <form class="form-horizontal">
	      	<div class="control-group" >
	        	<label>Q10 Grade: 5/5 </label>
	      	</div>
	      	
	       <div class="control-group" >
	       		<label>Q10 comment:</label>
	       		<p class="muted"> Great! </p>
	       	</div>
          </form>  
        
        <form>
          <label>Question about Grade?</label>
          	<div>	
            	<textarea rows="5" cols="100" class="span6"></textarea>
            </div>
            <br>
            <button class="btn">Send</button>
   
        </form>
        
        
        
        <div class="form-actions"><span class="pull-right">
          <button class="btn ">Finish</button>
          </span></div>
      </section>
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
</body>
</html>

<?php
	
?>
<html>
	<head>
	</head>
<?php
	
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

require_once 'util/lti_util.php';
require_once 'OAuth1p0.php';

/*
 *   CONFIGURATION
 *	 Create a key and secret that will be updated in the fields below inside of the single quotes. 
 *	 The site URL should be just the URL and not the complete path to the target page. 
 *	 The complete URL with target page will be entered inside of D2L Brightspace when setting up the remote plugin.
 */
$OAUTH_KEY    = 'key'; // You should use a better key! This is shared with the LMS
$OAUTH_SECRET = 'secret'; // You should use a better secret! This is shared with the LMS
$SITE_URL     = 'https://url_your_code_lives_at.com';

if(!isset($_REQUEST['lis_outcome_service_url'])
|| !isset($_REQUEST['lis_result_sourcedid'])
|| !isset($_REQUEST['oauth_consumer_key'])
) {
	    // If these weren't set then we aren't a valid LTI launch
	if(!isset($courseid)) {
        exit('This page was not launched via LTI from Brightspace. Make sure to launch the tutorials widget from within Brightspace.');
    }
} else {
    // Ok, we are an LTI launch.

    /*
     *   VERIFY OAUTH SIGNATURE
     */

    // The LMS gives us a key and we need to find out which shared secret belongs to that key.
    // The key & secret are configured on the LMS in "Admin Tools" > "External Learning Tools" > "Manage Tool Providers",
    // Or in the remote plugin setup page if you are using them.
    $oauth_consumer_key = $_REQUEST['oauth_consumer_key'];

    // We only have one key, "key", which corresponds to the (shared) secret "secret"
    if($oauth_consumer_key != $OAUTH_KEY) {
        exit("If you are seeing this message, something isn't quite right. Here are a couple of things to try. <ol><li>Try restarting your web browser.</li><li>Try clearing your web browser's cache. <a href='https://www.refreshyourcache.com/en/home/' target='_blank'>https://www.refreshyourcache.com</a></li><li>If all else fails, contact the Technology Assistance Center if you continue to see this message.");
    } else {
        $oauth_consumer_secret = $OAUTH_SECRET;
    }

    // Store things that Instructor Widget will need into the session
    session_start();
    $_SESSION['lis_outcome_service_url'] = $_REQUEST['lis_outcome_service_url'];
    $_SESSION['lis_result_sourcedid']    = $_REQUEST['lis_result_sourcedid'];
    $_SESSION['lis_person_name_given']   = $_REQUEST['lis_person_name_given'];
    $_SESSION['context_id']   			 = $_REQUEST['context_id'];
    $_SESSION['ext_d2l_role']   		 = $_REQUEST['ext_d2l_role'];
    $_SESSION['oauth_consumer_key']      = $_REQUEST['oauth_consumer_key'];
    $_SESSION['oauth_consumer_secret']   = $oauth_consumer_secret;
    session_write_close();
 }  
 ?>
 <link rel="stylesheet" href="css/style.css">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
 <?php

/*
 *   OPTIONAL LTI LAUNCH PARAMETERS
 *   Some LTI parameters are set by the LMS but may or may not be sent depending on security settings.
 *   One example is the user's given name. Check External Learning Tools to see if this is enabled for your links
 *   or disabled globally.
 *	 The following parameters provide the viewing user's first name, course OU number, and viewing user's role.
 */
if(isset($_REQUEST['lis_person_name_given'])) {
    $user = $_REQUEST['lis_person_name_given'];
}

if(isset($_REQUEST['context_id'])) {
    $courseid = $_REQUEST['context_id'];
}
if(isset($_REQUEST['ext_d2l_role'])) {
	$role = $_REQUEST['ext_d2l_role'];
}

/*
 * If the viewing user's role is one of the following and the courseid is null, show an "under construction" message.
 * If the viewing user's role is one of the following and the courseid exists, show the instructor information from the database.
 */
if($role == Instructor || $role == Admin) {
	echo "<h2><center>Course Setup Checklist</center></h2>";
	echo "<h4><center>Click on the buttons below for more information about each topic.</center></h4>";
	?>
<center><button onclick="showCopyImportDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Copy or import your course files</button></center>
<div id="copyImportDiv" style="display:none;">
	<br><p>Copy or import a course package that you have taught previously. Other course setup methods include the Course Builder, Course Design Accelerator, and the Instructional Design Wizard.
		<ul>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000030017-faculty-how2-copy-content-from-previous-semesters" onclick="trackOutboundLink('https://gfcmsuelearning.freshdesk.com/solution/articles/11000030017-faculty-how2-copy-content-from-previous-semesters'); return:false;" target="_blank" >Tutorial - Copy Content From Previous Semester</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000035066-faculty-how2-use-the-course-builder" target="_blank" onclick="trackOutboundLink('https://gfcmsuelearning.freshdesk.com/solution/articles/11000035066-faculty-how2-use-the-course-builder'); return:false;">Tutorial - How To Use Course Builder</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000035067-faculty-how-2-use-the-course-design-accelerator" target="_blank" onclick="trackOutboundLink('https://gfcmsuelearning.freshdesk.com/solution/articles/11000035067-faculty-how-2-use-the-course-design-accelerator'); return:false;">Tutorial - How To Use Course Design Accelerator</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000035068-faculty-how2-use-the-instructional-design-wizard" target="_blank" onclick="trackOutboundLink('https://gfcmsuelearning.freshdesk.com/solution/articles/11000035068-faculty-how2-use-the-instructional-design-wizard'); return:false;">Tutorial - How To Use Instructional Design Wizard</a></li>
		</ul>
	</p>
</div>
</br>
<center><button onclick="showSyllabusDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Create or review your syllabus</button></center>
<div id="SyllabusDiv" style="display:none;">
	<br><p>Your course syllabus needs to be uploaded to the Content area of your course. If you are creating a new syllabus, ensure you are using the correct accessible syllabus template located on <a href="http://facstaff.gfcmsu.edu/forms/academic.html" target="_blank">http://facstaff.gfcmsu.edu/forms/academic.html</a>.
		<ul>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000030732-faculty-how2-create-an-accessible-syllabus-using-the-syllabus-template" target="_blank">Tutorial - Using The Accessible Syllabus Template</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000030392-faculty-how2-upload-a-topic-" target="_blank">Tutorial - How To Upload Your Syllabus (or other documents) Into Content</a></li>
		</ul>
	</p>
</div>
</br>
<center><button onclick="showUpdateContentDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Add or update course content and activities</button></center>
<div id="UpdateContentDiv" style="display:none;">
	<br><p>Structure your course into units by using modules and topics, or create different learning activities.  Please ensure that your content is accessible by using the accessibility checker.
A recommended best practice is to use the quicklinks tool to link directly to discussions, assignments, and more.
		<ul>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000030393-faculty-how2-create-a-course-page-file-and-insert-stuff" target="_blank">Tutorial - How To Create A New Content Item</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000033018-faculty-how2-create-an-assignment-submission-folder" target="_blank">Tutorial - How To Create An Assignment Submission Folder</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000030021-faculty-how2-use-the-discussion-tool" target="_blank">Tutorial - How To Create Discussion Forums and Topics</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000033028-faculty-how2-create-a-new-quiz-with-new-questions" target="_blank">Tutorial - How To Create A New Quiz</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000032010-faculty-how2-use-the-accessibility-checker-within-d2l" target="_blank">Tutorial - How To Use The Accessibility Checker within D2L</a></li>
		</ul>
	</p>
</div>
</br>
<center><button onclick="showGradebookDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Manage your gradebook</button></center>
<div id="GradebookDiv" style="display:none;">
	<br><p>Set up grade items and categories, create rubrics, and associate grade items with learning outcomes.  Please see Policy 308.1 Grading in the GFCMSU Policy Manual for more information.
		<ul>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000030116-faculty-how2-use-the-grades-tool-in-d2l" target="_blank">Tutorial - How To Use The Grades Tool In Brightspace</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000047726-faculty-setting-up-a-weighted-gradebook" target="_blank">Tutorial - How To Setup A Weighted Gradebook</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000030018-faculty-how2-release-final-grades-in-d2l-s-gradebook" target="_blank">Tutorial - How To Release Final Grades To Students</a></li>
			
		</ul>
	</p>
</div>
</br>
<center><button onclick="showWelcomeMessageDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Post a welcome message</button></center>
<div id="WelcomeMessageDiv" style="display:none;">
	<br><p>Before posting announcements, make sure you have chosen your course homepage and updated your instructor widget.  Homepage announcements are excellent for sharing important information, getting started tips with students, or communicating changes in due dates.  Engage your students by creating a video message that welcomes students to the course.  Please communicate frequently (example: weekly) with your students.
		<ul>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000029729-faculty-how2-create-an-announcement-in-your-d2l-course" target="_blank">Tutorial - How To Create An Announcement In Your Course</a></li>
		</ul>
	</p>
</div>
</br>
<center><button onclick="showReviewClasslistDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Review the classlist & Activate your course</button></center>
<div id="ReviewClasslistDiv" style="display:none;">
	<br><p>The Classlist tool allows you to easily message students enrolled in your course.  Please verify that the Classlist matches your roster in Banner.  
		<ul>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000032032-faculty-how2-use-the-classlist-tool-in-d2l" target="_blank">Tutorial - How To Use The Classlist Tool in Brightspace</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000030077-faculty-how2-activate-a-course" target="_blank">Tutorial - How To Activate Your Course</a></li>
		</ul>
	</p>
</div>
</br>
<center><button onclick="showOtherCommonTasksDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Other common tasks</button></center>
<div id="OtherCommonTasksDiv" style="display:none;">
	<br><p>The tutorials below are related to some other common course setup tasks.  
		<ul>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000035687-faculty-combine-courses-request" target="_blank">Tutorial - How To Submit Course Combine Requests</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000044642-faculty-how-do-i-add-an-evaluator-to-my-course-" target="_blank">Tutorial - How To Add Evaluators To Your Course</a></li>
			<li><a href="http://gfcmsuelearning.freshdesk.com/solution/articles/11000033033-faculty-how2-adjust-the-quiz-submission-view" target="_blank">Tutorial - How To Allow Students To See Quiz Feedback</a></li>
		</ul>
	</p>
</div>

<?php
	} else if ($role == Student) {
		echo "<h2><center>Brightspace Tools Refresher</center></h2>";
		echo "<h4><center>Click on the buttons below for more information about each topic.</center></h4>";
		?>
	<center><button onclick="showContentDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Content</button></center>
<div id="ContentDiv" style="display:none;">
	<br><center><p>Navigate The Content Tool<br>
		<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://www.youtube.com/embed/IZtzwWJnIXU' frameborder='0' allowfullscreen></iframe></div>
	</p></center>
</div>
</br>
<center><button onclick="showDiscussionsDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Discussions</button></center>
<div id="DiscussionsDiv" style="display:none;">
	<br><center><p>Navigate and Participate in Discussions<br>
		<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://www.youtube.com/embed/9WAYt5jQAqc' frameborder='0' allowfullscreen></iframe></div>
	</p></center>
</div>
</br>
<center><button onclick="showAssignmentsDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Assignments</button></center>
<div id="AssignmentsDiv" style="display:none;">
	<br><center><p>Submit Assignments<br>
		<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://www.youtube.com/embed/CukqpBqC780' frameborder='0' allowfullscreen></iframe></div>
	</p></center>
</div>
</br>
<center><button onclick="showQuizzesDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Quizzes</button></center>
<div id="QuizzesDiv" style="display:none;">
	<br><center><p>Take Quizzes and Exams<br>
		<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://www.youtube.com/embed/gFHrKRoGImU' frameborder='0' allowfullscreen></iframe></div>
	</p></center>
</div>
</br>
<center><button onclick="showClasslistDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Classlist</button></center>
<div id="ClasslistDiv" style="display:none;">
	<br><center><p>Use Classlist To Send Emails in Brightspace<br>
		<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://www.youtube.com/embed/y2mv5vcgpns' frameborder='0' allowfullscreen></iframe></div>
	</p></center>
</div>
</br>
<center><button onclick="showClassProgressDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Class Progress</button></center>
<div id="ClassProgressDiv" style="display:none;">
	<br><center><p>Review Your Progress In Your Courses<br>  
		<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://www.youtube.com/embed/SfHW_QqNF-g' frameborder='0' allowfullscreen></iframe></div>
	</p></center>
</div>
</br>
<center><button onclick="showGradesDiv()" style="background-color: #3498db; color: white; border-radius: 4px; width: 100%;font-size: 20px;align-content:center; align-self:center;">Grades</button></center>
<div id="GradesDiv" style="display:none;">
	<br><center><p>View Your Current Grades In Your Courses<br>  
		<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='https://www.youtube.com/embed/XfCet4t8b5Q' frameborder='0' allowfullscreen></iframe></div>
	</p></center>
</div>



<?php
}

//var_dump($role);
?>
<script>
function showCopyImportDiv() {
	var div = document.getElementById("copyImportDiv");
	if (div.style.display === "none") {
	closeAllButCopyImport();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}	
function showSyllabusDiv() {
	var div = document.getElementById("SyllabusDiv");
	if (div.style.display === "none") {
	closeAllButSyllabusReview();
	div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}	
function showUpdateContentDiv() {
	var div = document.getElementById("UpdateContentDiv");
	if (div.style.display === "none") {
	closeAllButUpdateContent();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}	
function showGradebookDiv() {
	var div = document.getElementById("GradebookDiv");
	if (div.style.display === "none") {
	closeAllButGradebook();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}	
function showWelcomeMessageDiv() {
	var div = document.getElementById("WelcomeMessageDiv");
	if (div.style.display === "none") {
	closeAllButWelcomeMessage();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}	
function showReviewClasslistDiv() {
	var div = document.getElementById("ReviewClasslistDiv");
	if (div.style.display === "none") {
	closeAllButClasslist();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}	
function showOtherCommonTasksDiv() {
	var div = document.getElementById("OtherCommonTasksDiv");
	if (div.style.display === "none") {
	closeAllButOtherCommonTasks();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}

function showContentDiv() {
	var div = document.getElementById("ContentDiv");
	if (div.style.display === "none") {
	closeAllButContent();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}
function showDiscussionsDiv() {
	var div = document.getElementById("DiscussionsDiv");
	if (div.style.display === "none") {
	closeAllButDiscussions();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}
function showAssignmentsDiv() {
	var div = document.getElementById("AssignmentsDiv");
	if (div.style.display === "none") {
	closeAllButAssignments();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}
function showQuizzesDiv() {
	var div = document.getElementById("QuizzesDiv");
	if (div.style.display === "none") {
	closeAllButQuizzes();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}
function showClasslistDiv() {
	var div = document.getElementById("ClasslistDiv");
	if (div.style.display === "none") {
	closeAllButClasslist2();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}
function showClassProgressDiv() {
	var div = document.getElementById("ClassProgressDiv");
	if (div.style.display === "none") {
	closeAllButClassProgress();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}
function showGradesDiv() {
	var div = document.getElementById("GradesDiv");
	if (div.style.display === "none") {
	closeAllButGrades();
    div.style.display = "block";
  } else {
    div.style.display = "none";
  }
}




function closeAllButCopyImport() {
	document.getElementById('SyllabusDiv').style.display = "none";
	document.getElementById('UpdateContentDiv').style.display = "none";
	document.getElementById('GradebookDiv').style.display = "none";
	document.getElementById('WelcomeMessageDiv').style.display = "none";
	document.getElementById('ReviewClasslistDiv').style.display = "none";
	document.getElementById('OtherCommonTasksDiv').style.display = "none";
}
function closeAllButSyllabusReview() {
	document.getElementById('copyImportDiv').style.display = "none";
	document.getElementById('UpdateContentDiv').style.display = "none";
	document.getElementById('GradebookDiv').style.display = "none";
	document.getElementById('WelcomeMessageDiv').style.display = "none";
	document.getElementById('ReviewClasslistDiv').style.display = "none";
	document.getElementById('OtherCommonTasksDiv').style.display = "none";
}
function closeAllButUpdateContent() {
	document.getElementById('SyllabusDiv').style.display = "none";
	document.getElementById('copyImportDiv').style.display = "none";
	document.getElementById('GradebookDiv').style.display = "none";
	document.getElementById('WelcomeMessageDiv').style.display = "none";
	document.getElementById('ReviewClasslistDiv').style.display = "none";
	document.getElementById('OtherCommonTasksDiv').style.display = "none";
}
function closeAllButGradebook() {
	document.getElementById('SyllabusDiv').style.display = "none";
	document.getElementById('UpdateContentDiv').style.display = "none";
	document.getElementById('copyImportDiv').style.display = "none";
	document.getElementById('WelcomeMessageDiv').style.display = "none";
	document.getElementById('ReviewClasslistDiv').style.display = "none";
	document.getElementById('OtherCommonTasksDiv').style.display = "none";
}
function closeAllButWelcomeMessage() {
	document.getElementById('SyllabusDiv').style.display = "none";
	document.getElementById('UpdateContentDiv').style.display = "none";
	document.getElementById('GradebookDiv').style.display = "none";
	document.getElementById('copyImportDiv').style.display = "none";
	document.getElementById('ReviewClasslistDiv').style.display = "none";
	document.getElementById('OtherCommonTasksDiv').style.display = "none";
}
function closeAllButClasslist() {
	document.getElementById('SyllabusDiv').style.display = "none";
	document.getElementById('UpdateContentDiv').style.display = "none";
	document.getElementById('GradebookDiv').style.display = "none";
	document.getElementById('WelcomeMessageDiv').style.display = "none";
	document.getElementById('copyImportDiv').style.display = "none";
	document.getElementById('OtherCommonTasksDiv').style.display = "none";
}
function closeAllButOtherCommonTasks() {
	document.getElementById('SyllabusDiv').style.display = "none";
	document.getElementById('UpdateContentDiv').style.display = "none";
	document.getElementById('GradebookDiv').style.display = "none";
	document.getElementById('WelcomeMessageDiv').style.display = "none";
	document.getElementById('copyImportDiv').style.display = "none";
	document.getElementById('ReviewClasslistDiv').style.display = "none";
}


function closeAllButContent() {
	document.getElementById('DiscussionsDiv').style.display = "none";
	document.getElementById('AssignmentsDiv').style.display = "none";
	document.getElementById('QuizzesDiv').style.display = "none";
	document.getElementById('ClasslistDiv').style.display = "none";
	document.getElementById('ClassProgressDiv').style.display = "none";
	document.getElementById('GradesDiv').style.display = "none";
}
function closeAllButDiscussions() {
	document.getElementById('ContentDiv').style.display = "none";
	document.getElementById('AssignmentsDiv').style.display = "none";
	document.getElementById('QuizzesDiv').style.display = "none";
	document.getElementById('ClasslistDiv').style.display = "none";
	document.getElementById('ClassProgressDiv').style.display = "none";
	document.getElementById('GradesDiv').style.display = "none";
}
function closeAllButAssignments() {
	document.getElementById('DiscussionsDiv').style.display = "none";
	document.getElementById('ContentDiv').style.display = "none";
	document.getElementById('QuizzesDiv').style.display = "none";
	document.getElementById('ClasslistDiv').style.display = "none";
	document.getElementById('ClassProgressDiv').style.display = "none";
	document.getElementById('GradesDiv').style.display = "none";
}
function closeAllButQuizzes() {
	document.getElementById('DiscussionsDiv').style.display = "none";
	document.getElementById('AssignmentsDiv').style.display = "none";
	document.getElementById('ContentDiv').style.display = "none";
	document.getElementById('ClasslistDiv').style.display = "none";
	document.getElementById('ClassProgressDiv').style.display = "none";
	document.getElementById('GradesDiv').style.display = "none";
}
function closeAllButClasslist2() {
	document.getElementById('DiscussionsDiv').style.display = "none";
	document.getElementById('AssignmentsDiv').style.display = "none";
	document.getElementById('QuizzesDiv').style.display = "none";
	document.getElementById('ContentDiv').style.display = "none";
	document.getElementById('ClassProgressDiv').style.display = "none";
	document.getElementById('GradesDiv').style.display = "none";
}
function closeAllButClassProgress() {
	document.getElementById('DiscussionsDiv').style.display = "none";
	document.getElementById('AssignmentsDiv').style.display = "none";
	document.getElementById('QuizzesDiv').style.display = "none";
	document.getElementById('ClasslistDiv').style.display = "none";
	document.getElementById('ContentDiv').style.display = "none";
	document.getElementById('GradesDiv').style.display = "none";
}
function closeAllButGrades() {
	document.getElementById('DiscussionsDiv').style.display = "none";
	document.getElementById('AssignmentsDiv').style.display = "none";
	document.getElementById('QuizzesDiv').style.display = "none";
	document.getElementById('ClasslistDiv').style.display = "none";
	document.getElementById('ClassProgressDiv').style.display = "none";
	document.getElementById('ContentDiv').style.display = "none";
}



</script>
</html>
<?php
?>
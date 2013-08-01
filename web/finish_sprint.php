<?php
if(isset($_POST)){
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//require_once("models/header.php");
$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");
$token = $_GET['project'];
//get the Project
$project = mysqli_query($connection, "SELECT * FROM "."Project WHERE ProjectName = '$token'");
$projectInfo = mysqli_fetch_array($project);

//Stores ProjectID, name, datecreated and date completed into variables
$ProjectID = $projectInfo['ProjectID'];
$ProjectName = $projectInfo['ProjectName'];
$DateCreated = $projectInfo['DateCreated'];
$DateCompleted = $projectInfo['DateCompleted'];
$Creator = $projectInfo['Creator'];

	foreach($_POST as $key => &$val) {
    $val = mysql_real_escape_string($val);
   }
   
   //gets the Current Sprint Number
   $sprintNumQry = mysqli_query($connection, "SELECT SprintNum FROM "."Sprint WHERE ProjectID = '$ProjectID' ORDER BY SprintNum ASC ");
	if($sprintNumQry != NULL){
		$currentSprint = NULL;
		while ($sprintNumber = mysqli_fetch_array($sprintNumQry)){
			$currentSprint = $sprintNumber['SprintNum'];
		}
	}

	//Updates Sprint's finish date current date/time and sets its status to Finished.
	$qry =	"UPDATE "."Sprint 
			SET FinishTime = CURDATE(), SprintStatus = '0'
			WHERE ProjectID = $ProjectID AND SprintNum = $currentSprint";
	
	mysqli_query($connection, $qry);
	
echo "
	<body>
		<h2>End Sprint</h2>";
echo "
		<div id='EndSprintBox'>
		<form name='EndSprint' action='finish_sprint.php?finish_sprint="
    .$ProjectName."' method='post'>



		</form>
	</body>";	
}	
?>

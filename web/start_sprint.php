<?php
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");
echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb",
    "29G8l!06J82ofpPw","2013teamb");

//What is this for?
$isProjectOwner = $loggedInUser->checkPermission(array(2));
//session_start();
$token = $_REQUEST['Start_Sprint'];

$Qry="SELECT ProjectID, ProjectName FROM Project WHERE ProjectName = '$token'";
$getProjectInfo = mysqli_query($connection, $Qry);
$projectInfo = mysqli_fetch_array($getProjectInfo);
$ProjectID = $projectInfo['ProjectID'];
$ProjectName = $projectInfo['ProjectName'];

//assuming a sprint is keeping a history
/*
 *  If sprint is started all In Progress items will be pulled in.
 *  if item is updated to in progress while sprint is started - will it be 
 *  expected to be included to current sprint?
 *  or will it be picked up when the current sprint is closed and a new sprint 
 *  is created
 */
//First check to see if Sprint(s) exist increment else create initial
$Qry="SELECT SprintNum FROM Sprint WHERE SprintStatus ='1' AND ProjectID='".$ProjectID."'";
if($result=mysqli_query($connection, $Qry)){
    $fieldinfo=mysqli_fetch_field($result);
    if($fieldinfo->name>1){
        //Increment Sprint number
        $SprintNum=1+$fieldinfo->name;

    }else{
        //Create Initial Sprint
        $SprintNum=1;
    }
    mysqli_free_result($result);
}else{
	die('Error: ' . mysqli_error($connection));
}

//Any items in progress will be placed in current sprint
//sprint status is started
//PO must specify end date - can this be changed no but it is not set in stone
//Will enddate start next sprint automatically? NO
//Will changing status to finished automatically start the next sprint? 
//	NO - Must start Sprint process
//-iff there are items to work on or project is open? - create new sprint?

//get the Estimated completion date

$day = $_REQUEST['Day'];
$month = $_REQUEST['Month'];
$year = $_REQUEST['Year'];
$EstCompDate = $year."-".$month."-".$day; //add this functionality later


/*
$day = $_SESSION['Day'];
$month = $_SESSION['Month'];
$year = $_SESSION['Year'];
$EstCompDate = $year."-".$month."-".$day; //add this functionality later
*/
echo $EstCompDate ."<br>";


//$EstCompDate ="2013-07-29";
echo "ProjectID = $ProjectID<tab>SprintNum=$SprintNum<tab>EstCompDate=$EstCompDate<br>";

//SprintStatus=1 Completed, SprintStatus=0 Not Completed
$Qry="INSERT INTO Sprint(ProjectID,SprintNum,EstCompDate,SprintStatus)
	  VALUES('$ProjectID','$SprintNum','$EstCompDate','1')";
if(!mysqli_query($connection, $Qry)){
    die('Error: ' . mysqli_error($connection));
}


//Loop through Items to add sprint id
//ContainerID,Status,Priority,Type
$Qry="SELECT ItemID FROM Item WHERE ProjectID='$ProjectID' AND Status='1'";
if($result = mysqli_query($connection, $Qry)){
    while ($fieldinfo=mysqli_fetch_array($result)){
        $uQry="UPDATE Item SET Sprint='$SprintNum' WHERE ItemID='".$fieldinfo['ItemID']."'";
        if (!mysqli_query($connection, $uQry)){
        	die('Error: ' . mysqli_error($connection));
        }
    }
    mysqli_free_result($result);
}
/*
//display sprint metrics
$Qry="SELECT * "
	."FROM Item,Sprint "
	."WHERE Item.ProjectID=Sprint.ProjectID AND Item.ProjectID='$ProjectID' "
	."AND SprintNum=".$SprintNum;
	$temp =mysqli_query($connection, $Qry);
while($result = mysqli_fetch_array($temp)){
    switch ($result['SprintStatus']){
        case 0:
            printf("Sprint%d Started on %s\n",$SprintNum,$result['StartTime']);
            echo"<br>";
            $Status="Started";
            break;
        case 1:
            printf("Sprint%d Started on %s\n",$SprintNum,$result['StartTime']);
            echo"<br>";
            printf("Sprint%d Finished on %s\n",$SprintNum,$result['EstCompDate']);
            echo"<br>";
            $Status="Finished";
            break;
    }
    switch ($result['Type']){
        case 0: $Type="Story"; break;
        case 1: $Type="Chore"; break;
        case 2: $Type="Bug";   break;
    }
	
	$Qry2="SELECT display_name FROM uc_users WHERE id=".$result['Creator'];
	$userInfo=mysqli_query($connection, $Qry2);
	$userID = mysqli_fetch_array($userInfo);
    printf("Item Name: %s\nCurrent Status: %s\nCreator: %s\nPriority: %d\nType: %s\nDescription: %s\n",
        $result['ItemName'],$Status,$userID['display_name'],$result['Priority'],$Type,$result['Description']);
        echo"<br>";
}

echo "
	<body>
		<h2>Start Sprint</h2>";
echo "
		<div id='startSprintBox'>
		<form name='startSprint' action='start_sprint.php?start_sprint=".$ProjectName."' method='post'>



		</form>
	</body>";
*/

?>

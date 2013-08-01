<?php
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

//Add new Project entry
if(!empty($_POST)){

	$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");
	$errors = array();
	$projectName = trim($_POST["projectName"]);
	$same = 0;

	//Grab Project Table.
	$listOfProjects = mysqli_query($connection, "SELECT * FROM "."Project");
	
	//Check to make sure projectName does not exist.
	while($row = mysqli_fetch_array($listOfProjects)){
		$compName = $row['ProjectName'];
		//Case insensitive check
		if(strcasecmp ($compName , $projectName) == 0){
			$same = 1;
		}
	}
	
	//Retrieve the UserID of the manager creating the projecy.
	$userID = $loggedInUser->user_id;
	
	//Insert into the project table if name doesn't exists and not a blank username
	if($projectName == ''){
		echo "Blank Project names are not allowed";
	}elseif(!$same){
		$insertProj = "INSERT INTO "."Project(ProjectName, Creator)
		VALUES('$projectName', '$userID')";
		if(!mysqli_query($connection, $insertProj)){
			die('Error: ' . mysqli_error($connection));
		}
		
		/*//Grab the new Project table
		$result = mysqli_query($connection, "SELECT * FROM "."Project");
	
		//Loop to find the projectID just made.
		while($array = mysqli_fetch_array($result)){
			$projectID = $array['ProjectID'];
		}*/
		
		//getting projectID
		$result = mysqli_query($connection, "SELECT * FROM "."Project WHERE ProjectName = '$projectName'");
		$array = mysqli_fetch_array($result);
		$projectID = $array['ProjectID'];
		
		//HISTORY LOG
		$userHistResult = mysqli_query($connection, "SELECT * FROM "."uc_users WHERE id = '$userID'");
		$hArray = mysqli_fetch_array($userHistResult );
		$userDisplayName = $hArray ['display_name'];
		$HistoryDes = "Project Created by $userDisplayName.";
		$projectLog = "INSERT INTO "."ProjectLog(UserID, ProjectID, Description)
		VALUES('$userID', '$projectID', '$HistoryDes')";
		if(!mysqli_query($connection, $projectLog )){
			die('Error: ' . mysqli_error($connection));
		}

				
		//Insert new projectID and UserID into the ProjectUser table
		$insertProjUser = "INSERT INTO "."ProjectUser(ProjectID, UserID, Role)
							VALUES( '$projectID', '$userID', '1')";
		if(!mysqli_query($connection, $insertProjUser)){
			die('Error: ' . mysqli_error($connection));
		}
	}else{
		echo "Project Name already exists";
	}
}

//Left Nav Bar
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h2>Create Project</h2>

<div id='left-nav'>";
include("left-nav.php");
echo "
</div>
<div id='main'>";
echo "
<div id='newProjBox'>
<form name='newProject' action='".$_SERVER['PHP_SELF']."' method='post'>

<p>
<label>Project Name:</label>
<input type='text' name='projectName' />
</p>
<p>
<label>&nbsp;<br>
<input type='submit' value='Create'/>
</p>

</form>
</div>

</div>
<div id='bottom'></div>
</div>
</body>
</html>";
?>

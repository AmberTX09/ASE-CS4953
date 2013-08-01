<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");
//require_once("start_sprint.php");

$isAdmin = $loggedInUser->checkPermission(array(2));

$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");
$token = $_GET['project'];

//get the Project
$project = mysqli_query($connection, "SELECT * FROM "."Project WHERE ProjectName = '$token'");
$projectInfo = mysqli_fetch_array($project);

$ProjectID = $projectInfo['ProjectID'];
$ProjectName = $projectInfo['ProjectName'];
$DateCreated = $projectInfo['DateCreated'];
$DateCompleted = $projectInfo['DateCompleted'];
$Creator = $projectInfo['Creator'];


//if form is posted
if(!isset($_POST['submit']))
{	
	unset($_POST['sumbit']);
		
	//update project name
	if(!empty($_POST['projectName']) && $_POST['projectName'] != $token){
		$newName = trim($_POST['projectName']);
		$same = 0;

		//Grab Project Table.
		$listOfProjects = mysqli_query($connection, "SELECT * FROM "."Project");
		
		//Check to make sure projectName does not exist.
		while($row = mysqli_fetch_array($listOfProjects)){
			$compName = $row['ProjectName'];
			//Case insensitive check
			if(strcasecmp ($compName , $newName ) == 0){
				$same = 1;
			}
		}
		
		//Insert into the project table if name doesn't exists and not a blank username
		if($newName == ''){
			echo "Blank Project names are not allowed";
		}elseif(!$same){
			$insertProj = "UPDATE "."Project SET ProjectName ='$newName' WHERE ProjectName = '$token'";
				if(!mysqli_query($connection, $insertProj)){
					die('Error: ' . mysqli_error($connection));
				}
				
				$token = $newName;
		}else{
			echo "Project Name already exists";
		}

	}
		
	//add user to team
	if(!empty($_POST['newMember'])){
		foreach($_POST['newMember'] as $newMemberToken){
			if($newMemberToken != "NULL"){
				$newUser = mysqli_query($connection, "SELECT * FROM "."uc_users WHERE display_name = '$newMemberToken'");
				$newUserInfo = mysqli_fetch_array($newUser);
				$newUserID = $newUserInfo['id'];
				$insertProjUser = "INSERT INTO "."ProjectUser(ProjectID, UserID, Role)
										VALUES( '$ProjectID', '$newUserID', '0')";
					if(!mysqli_query($connection, $insertProjUser)){
						die('Error: ' . mysqli_error($connection));
					}
			}
		}
	}
	
	//change user role
	if(!empty($_POST['role'])){
		foreach($_POST['role'] as $role){
			//$blocks = "";

			$blocks = str_split($role, (strlen($role) - 1));
			$id = $blocks[0];
			$key = $blocks[1];

			if($key == 1){
				if($id != $Creator || $id == $Creator && $isAdmin){
					$user = mysqli_query($connection, "SELECT * FROM "."ProjectUser WHERE UserID = '$id' AND ProjectID = '$ProjectID'");
					$userInfo = mysqli_fetch_array($user);
					$newRole = ($userInfo['Role']+1)%2;
					$insertNewRole = "UPDATE "."ProjectUser
										SET "."ProjectUser.Role = '$newRole'
										WHERE "."ProjectUser.UserID = '$id'
										AND "."ProjectUser.ProjectID = '$ProjectID'";
						if(!mysqli_query($connection, $insertNewRole )){
							die('Error: ' . mysqli_error($connection));
						}
				}else{
					print "Unable to change creator permissions.  Please contact system administrator.";
				}
			}
	
		}
	}
	
	
	//delete teammember
	if(!empty($_POST['deleteMember'])){
	
		foreach($_POST['deleteMember'] as $memberToDelete){
		
			$delUser = mysqli_query($connection, "SELECT * 
													FROM "."uc_users 
													WHERE display_name = '$memberToDelete'");
													
			$delUserInfo = mysqli_fetch_array($delUser);
			$delUserID = $delUserInfo['id'];
			if($delUserID == $Creator && !$isAdmin){
				print "Unable to remove project creator.  Please contact system administrator.";
				break;
			}
			$deleteProjUser = "DELETE FROM "."ProjectUser 
								WHERE UserID = '$delUserID' AND ProjectID = '$ProjectID'";
								
			if(!mysqli_query($connection, $deleteProjUser)){
				die('Error: ' . mysqli_error($connection));
			}
		}
	}
	
	//complete/reactivate project
	if(!empty($_POST['completeProject'])){
		mysqli_query($connection, "UPDATE "."Project 
									SET DateCompleted = CURDATE() 
									WHERE "."Project.ProjectID = $ProjectID");
		
	}
	if(!empty($_POST['reactivateProject'])){
		mysqli_query($connection, "UPDATE "."Project 
									SET DateCompleted = NULL 
									WHERE "."Project.ProjectID = $ProjectID");
		
	}
}

if($ProjectName != $token || !empty($_POST['completeProject']) || !empty($_POST['reactivateProject'])){

	//get the Project
	$project = mysqli_query($connection, "SELECT * FROM "."Project WHERE ProjectName = '$token'");
	$projectInfo = mysqli_fetch_array($project);
	
	$ProjectID = $projectInfo['ProjectID'];
	$ProjectName = $projectInfo['ProjectName'];
	$DateCreated = $projectInfo['DateCreated'];
	$DateCompleted = $projectInfo['DateCompleted'];
	$Creator = $projectInfo['Creator'];
}

//get the ProjectUser list for this project
$projectUsers = mysqli_query($connection, "SELECT * FROM "."ProjectUser WHERE ProjectID = '$ProjectID'");
$projUsrTmp = mysqli_query($connection, "SELECT * FROM "."ProjectUser WHERE ProjectID = '$ProjectID'");

$userRole = NULL;
//Check if user has access rights to page
while($userList = mysqli_fetch_array($projUsrTmp)){
	if ($userList['UserID'] == $loggedInUser->user_id){
		$userRole = $userList['Role'];
	}
}
//if user does not have rights, move user to account.php
if($userRole == NULL && !$isAdmin){
	header("Location: account.php"); die();
}

//----------Sprint Implementation

//Get the Current Sprint:
$sprintNumQry = NULL;
$sprintNumQry = mysqli_query($connection, "SELECT SprintNum FROM "."Sprint WHERE ProjectID = '$ProjectID' ORDER BY SprintNum ASC ");
if($sprintNumQry != NULL){
	$currentSprint = NULL;
	while ($sprintNumber = mysqli_fetch_array($sprintNumQry)){
		$currentSprint = $sprintNumber['SprintNum'];
	}
}
//echo "$currentSprint";

//get status of Current Sprint:
$sprintStatus = NULL;
if($sprintNumQry != NULL){
	$qry =  "SELECT *
		FROM "."Sprint 
		WHERE ProjectID = '$ProjectID' 
		AND SprintNum = '$currentSprint'";
	$sprint = mysqli_query($connection, $qry);
	$temp = mysqli_fetch_array($sprint);
	$sprintStatus = $temp['SprintStatus'];
}

/*if (!empty($_POST['FinishForm'])){
echo "<h1> test </h1>";



}*/


//Left Nav bar
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<div id='left-nav'>";
include("left-nav.php");

//Main Content
echo "
</div>
<div id='main'>";

if($userRole == 1 || $isAdmin){
echo"
	<form name='projectInfo' action='".$_SERVER['PHP_SELF']."?project=".$ProjectName."' id='update' method='post'>
	<table class='admin'><tr><td>
	<h3>Project Information</h3>
	<p>
	<b>Name:</b> 
	<input type='text' name='projectName' form = 'update' value='".$ProjectName."'/>
	</p>
	<p>";
	 //Shows the Start button if a sprint has not been started yet.
	 if($sprintNumQry != NULL){ 
		if ($sprintStatus == 0){
			//session_start();
			
			//Creates the Drop down form for the User to Select Day, Month, and Year of Sprint
			echo"
			Start Sprint:<br>
			Estimated Completion date:<br>
			<FORM method='post'  action = 'start_sprint.php' id = 'Start' name='myForm'>
			 Year:
			<select id='myYear' name='Year'>
				<option value='2013'>2013</option>
				<option value='2014'>2014</option>
			</select></br>
			Month:
			<select id='myMonth' name='Month'>
				 <option value ='1'>01-Jan</option>
				 <option value ='2'>02-Feb</option>  
				 <option value ='3'>03-Mar</option>
				 <option value ='4'>04-Apr</option>
				 <option value ='5'>05-May</option>
				 <option value ='6'>06-Jun</option>
				 <option value ='7'>07-July</option>  
				 <option value ='8'>08-Aug</option>
				 <option value ='9'>09-Sept</option>
				 <option value ='10'>10-Oct</option>
				 <option value ='11'>11-Nov</option>
				 <option value ='12'>12-Dec</option>  
			</select>
			Day:
			<select id='myDay' name='Day'>
			  <option>01</option>
			  <option>02</option>  
			  <option>03</option>
			  <option>04</option>
			  <option>05</option>
			  <option>06</option>
			  <option>7</option>  
			  <option>8</option>
			  <option>9</option>
			  <option>10</option>
			  <option>11</option>
			  <option>12</option>
			  <option>13</option>
			  <option>14</option>
			  <option>15</option>
			  <option>16</option>
			  <option>17</option>
			  <option>18</option>
			  <option>19</option>
			  <option>20</option> 
			  <option>21</option>
			  <option>22</option>
			  <option>23</option>
			  <option>24</option>
			  <option>25</option>
			  <option>26</option>
			  <option>27</option>
			  <option>28</option>
			  <option>29</option> 
			  <option>30</option> 
			  <option>31</option> 
			</select>
			<p><input type = 'submit' value ='Start Sprint'  name= 'Start_Sprint' class = 'submit'/></p></br>
			<form/>";  
				/*$_SESSION['Start_Sprint'] = $ProjectName;
				$_SESSION['myDay'] = $_POST['myDay'];
				$_SESSION['myMonth'] = $_POST['myMonth'];
				$_SESSION['myYear'] = $_POST['myYear'];*/
				
				//echo $_POST['month'];
			
		}else{
			//If a Sprint is currently in progress, option to Finish the Sprint
			//<input type = 'submit' form = 'FinishForm' value = 'Finish Sprint' name = 'FinishSprint'><br>
			echo"
				<input type = 'hidden' name = 'SprintFinish' id = 'SprintFinish' value = '0'/>
				<form method='post'  action ='finish_sprint.php' name='FinishForm' >
				<input type = 'button' value = 'Finish Sprint' 
				</form>
				
			";
		}
	}else{ 
		//show start sprint
		echo"fill in later";
	}
	
echo"
	<a href='create_item.php?create_item=".$ProjectName."'>Create Item</a><br />
	<a href='view_item.php?view_item=".$ProjectName."'>View Item List</a>
	<br>
	</p>
	<p>
	<b>ID:</b> ".$ProjectID ."
	</p>
	<p>
	<b>Creation Date:</b> ".$DateCreated ."
	</p>
	<p>
	<b>Completion Date:</b> ";
	if($DateCompleted == NULL){
		echo "Project Active
		<br>
		Complete Project <input type='checkbox' name='completeProject' form = 'update' value='completeProject'>
		";
	}else{
		echo "$DateCompleted
		<br>
		Reactivate Project <input type='checkbox' name='reactivateProject' form = 'update' value='completeProject'>
		";
	}
	echo"
	</p>
	
	<p><input type='submit' value='Update Project' form = 'update' class='submit'/></p>

	</td><td>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	</td><td>
	
	<td><h3>Project Team <p>Check to remove member</p></h3>
	<p>
	";
	
	//list all users in project
	$userNum =0;
	while($userList = mysqli_fetch_array($projectUsers)){
		$UserID = $userList['UserID'];
		
		//get user info
		$userInfo = mysqli_query($connection, "SELECT * FROM "."uc_users WHERE id = '$UserID'");
		$user = mysqli_fetch_array($userInfo);
		echo"
		<input type='checkbox' name='deleteMember[".$userNum."]' form = 'update' value='".$user['display_name']."'>
		".$user['display_name']."";

		if($userList['Role'] == 0){
			echo"<select name='role[".$userNum."]' form = 'update'>
			<option selected value ='".$user['id']."0'>Team Member</option>
			<option value ='".$user['id']."1'>Project Manager</option>
			</select>";
		}else{
			echo"<select name='role[".$userNum."]' form = 'update'>
			<option selected value ='".$user['id']."0'>Project Manager</option>
			<option value ='".$user['id']."1'>Team Member</option>
			</select>";
		}
		echo"
		</p>
		<p>";
		$userNum++;
	}
	echo"
	</td><td>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	</td><td>
	
	
	<h3>Add User To Team</h3>
	<p>
	<select multiple name='newMember[]' form = 'update'>
	<option  value='NULL' selected = 'selected'></option>";
	//get all users not in team by looping through all users and comparing against team members
	$allUsers = mysqli_query($connection, "SELECT * FROM "."uc_users");
	while($user1 = mysqli_fetch_array($allUsers)){
		$userFound = 0;
		$team = mysqli_query($connection, "SELECT * FROM "."ProjectUser WHERE ProjectID = '$ProjectID'");
		while($user2 = mysqli_fetch_array($team)){
			if($user1['id'] == $user2['UserID']){
				$userFound = 1;
				break;
			}
		}
		//display a non-team member
		if($userFound == 0){
			echo"<option value='".$user1['display_name']."'>".$user1['display_name']."</option>";
		}
	}
	echo"</select></form>";
	
}else{
echo"
	<table class='admin'><tr><td>
	<h3>Project Information</h3>
	<p>
	<b>Name:</b> ".$ProjectName."
	</p>
	<p>
	<a href='create_item.php?create_item=".$ProjectName."'>Create Item</a><br />
	<a href='view_item.php?view_item=".$ProjectName."'>View Item List</a>
	<br>
	</p>
	<p>
	<b>ID:</b> ".$ProjectID ."
	</p>
	<p>
	<b>Creation Date:</b> ".$DateCreated ."
	</p>
	<p>
	<b>Completion Date:</b> ";
	if($DateCompleted == NULL){
		echo "Project Active";
	}else{
		echo "".$DateCompleted;
	}
	echo"
	</p>
	</td>
	
	<td></td>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	</td><td>
	
	<td><h3>Project Team</h3>
	<p>
	";
	
	//list all users in project
	while($userList = mysqli_fetch_array($projectUsers)){
		$UserID = $userList['UserID'];
		
		//get user info
		$userInfo = mysqli_query($connection, "SELECT * FROM "."uc_users WHERE id = '$UserID'");
		$user = mysqli_fetch_array($userInfo);
		echo"
		".$user['display_name']."";
		if($userList['Role'] == 0){echo", Team Member";}else{echo", Project Owner";}echo"
		</p>
		<p>";
	}
}

echo"
</td>
</tr>
<br>
<br>


";
$items = $loggedInUser->grabItems();
echo "

<div style = 'width:1200px;height:300px;'>
<div style = 'display:inline-block; width:300px;position:absolute;height:300px;background-color:cornsilk;opacity:0.75;overflow-y:auto;'>
<span><center><strong style='font-size:x-large;'> Limbo</strong> </center></span><br>
<form>

";

foreach ($items as $row ) {
if ($row['ProjectID'] == $ProjectID && $row['ContainerID'] == 0){
echo  "<a href='item.php?item=".$row['ItemName']."'>Item Name:".$row['ItemName']." ID: ".$row['ItemID']."</a>
<br>";



}

}

echo "
</form>

</div>


<div style = 'display:inline-block;left:580px; width:300px;position:absolute;
height:300px;background-color:cornsilk;opacity:0.75;overflow-y:auto;'>
<span><center><strong style='font-size:x-large;'>Product Backlog</strong> </center></span><br>
<form>

";

foreach ($items as $row ) {
if ($row['ProjectID'] == $ProjectID && $row['ContainerID'] == 1){
echo  "<a href='item.php?item=".$row['ItemName']."'>Item Name:".$row['ItemName']." ID: ".$row['ItemID']."</a>
<br>";


}

}

echo "
</form>


</div>


<div style = 'display:inline-block;left:900px; width:300px;position:absolute;height:300px;background-color:cornsilk;opacity:0.75;overflow-y:auto;'>
<span><center><strong style='font-size:x-large;'>Current Sprint</strong> </center></span><br>
<form>

";

foreach ($items as $row ) {
if ($row['ProjectID'] == $ProjectID && $row['ContainerID'] == 2){
echo  "<a href='item.php?item=".$row['ItemName']."'>Item Name:".$row['ItemName']." ID: ".$row['ItemID']."</a>
<br>";


}

}

echo "
</form>


</div>

</div>
";

//ends main
echo"
</div>
<div id='bottom'></div>
</div>
</body>
</html>";



?>
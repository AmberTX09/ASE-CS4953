<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");


//$connection = mysqli_connect("localhost","root","root","2013teamb");
$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");
$token = $_GET['item'];


$isAdmin = $loggedInUser->checkPermission(array(2));
//get the ProjectUser list for this project
$projUsrTmp = mysqli_query($connection, "SELECT * FROM "."ProjectUser WHERE ProjectID = '$'");

$userRole = NULL;
//Check if user has access rights to page
while($userList = mysqli_fetch_array($projUsrTmp)){
	if ($userList['UserID'] == $loggedInUser->user_id){
		$userRole = $userList['Role'];
	}
}


//get the Item
$item = mysqli_query($connection, "SELECT * FROM "."Item WHERE ItemName = '$token'");
$itemInfo = mysqli_fetch_array($item);

$ItemID = $itemInfo['ItemID'];
$ItemName = $itemInfo['ItemName'];
$Status = $itemInfo['Status'];
$CompletionTime = $itemInfo['CompletionTime'];
$Creator = $itemInfo['Creator'];
$Type = $itemInfo['Type'];
$Priority = $itemInfo['Priority'];
$Description = $itemInfo['Description'];
$ContainerID = $itemInfo['ContainerID'];
$ProjectID = $itemInfo['ProjectID'];

//get the Project Name
	$project = mysqli_query($connection, "SELECT * FROM "."Project WHERE ProjectID = '$ProjectID '");
	$projectInfo = mysqli_fetch_array($project);
	$ProjectName = $projectInfo['ProjectName'];


//if form is posted
if(!isset($_POST['submit']))
{	
	unset($_POST['sumbit']);
		
	//update item name
	if(!empty($_POST['itemName']) && $_POST['itemName'] != $token){
		$newName = trim($_POST['itemName']);
		$same = 0;

		//Grab Item Table.
		$listOfItems = mysqli_query($connection, "SELECT * FROM "."Item");
		
		//Check to make sure itemName does not exist.
		while($row = mysqli_fetch_array($listOfItems)){
			$compName = $row['ItemName'];
			//Case insensitive check
			if(strcasecmp ($compName , $newName ) == 0){
				$same = 1;
			}
		}
		
		//Insert into the item table if name doesn't exists and not a blank username
		if($newName == ''){
			echo "Blank Item names are not allowed";
		}elseif(!$same){
			$insertProj = "UPDATE "."Item SET ItemName ='$newName' WHERE ItemName = '$token'";
				if(!mysqli_query($connection, $insertProj)){
					die('Error: ' . mysqli_error($connection));
				}
				
				$token = $newName;
		}else{
			echo "Item Name already exists";
		}
	}
	
	//complete/reactivate item
	if(!empty($_POST['completeItem'])){
		mysqli_query($connection, "UPDATE "."Item 
									SET CompletionTime = CURDATE() 
									WHERE "."Item.ItemID = $ItemID");
		
	}
	if(!empty($_POST['reactivateItem'])){
		mysqli_query($connection, "UPDATE "."Item 
									SET CompletionTime = NULL 
									WHERE "."Item.ItemID = $ItemID");
		
	}
	
	//update item status
	if(!empty($_POST['status'])){
		$newStatus = $_POST['status'];
		mysqli_query($connection, "UPDATE "."Item 
									SET Status = '$newStatus'
									WHERE "."Item.ItemID = $ItemID");
	}
	
	//update item container
a	if(!empty($_POST['container'])){
		$newContainer = $_POST['container'];
		mysqli_query($connection, "UPDATE "."Item 
									SET ContainerID = '$newContainer '
									WHERE "."Item.ItemID = $ItemID");
	}
	
	//update priority
	if(!empty($_POST['priority'])){
		$newPriority = $_POST['priority'];
		mysqli_query($connection, "UPDATE "."Item 
									SET Priority = '$newPriority'
									WHERE "."Item.ItemID = $ItemID");
	}
	
	//update completion time
	if(!empty($_POST['estComp'])){
		$newEst = $_POST['estComp'];
		mysqli_query($connection, "UPDATE "."Item 
									SET CompletionTime = '$newEst '
									WHERE "."Item.ItemID = $ItemID");
	}
	
	//update description
	if(!empty($_POST['itemDescription'])){
		$newDescrip = $_POST['itemDescription'];
		mysqli_query($connection, "UPDATE "."Item 
									SET Description = '$newDescrip '
									WHERE "."Item.ItemID = $ItemID");
	}


	if($_POST['submit'] == "Claim Task"){
		//$claim = $_POST['submit'];
		mysqli_query($connection, "UPDATE "."Task
									SET CurrentUser = '$loggedInUser->user_id'
									WHERE "."Task.ItemID = $ItemID");
	}elseif($_POST['submit'] == "Unclaim Task"){
		mysqli_query($connection, "UPDATE "."Task
									SET CurrentUser = '$claim '
									WHERE "."Task.ItemID = $ItemID");
	}
	

/*		
	//add user to team
	if(!empty($_POST['newMember'])){
		foreach($_POST['newMember'] as $newMemberToken){
			if($newMemberToken != "NULL"){
				$newUser = mysqli_query($connection, "SELECT * FROM "."uc_users WHERE display_name = '$newMemberToken'");
				$newUserInfo = mysqli_fetch_array($newUser);
				$newUserID = $newUserInfo['id'];
				$insertProjUser = "INSERT INTO "."ItemUser(ItemID, UserID, Role)
										VALUES( '$ItemID', '$newUserID', '0')";
					if(!mysqli_query($connection, $insertProjUser)){
						die('Error: ' . mysqli_error($connection));
					}
			}
		}
	}*/
	
/*	//change user role
	if(!empty($_POST['role'])){
		foreach($_POST['role'] as $role){
			//$blocks = "";

			$blocks = str_split($role, (strlen($role) - 1));
			$id = $blocks[0];
			$key = $blocks[1];

			if($key == 1){
				if($id != $Creator || $id == $Creator && $isAdmin){
					$user = mysqli_query($connection, "SELECT * FROM "."ItemUser WHERE UserID = '$id' AND ItemID = '$ItemID'");
					$userInfo = mysqli_fetch_array($user);
					$newRole = ($userInfo['Role']+1)%2;
					$insertNewRole = "UPDATE "."ItemUser
										SET "."ItemUser.Role = '$newRole'
										WHERE "."ItemUser.UserID = '$id'
										AND "."ItemUser.ItemID = '$ItemID'";
						if(!mysqli_query($connection, $insertNewRole )){
							die('Error: ' . mysqli_error($connection));
						}
				}else{
					print "Unable to change creator permissions.  Please contact system administrator.";
				}
			}
	
		}
	}*/
	
	
/*	//delete teammember
	if(!empty($_POST['deleteMember'])){
	
		foreach($_POST['deleteMember'] as $memberToDelete){
		
			$delUser = mysqli_query($connection, "SELECT * 
													FROM "."uc_users 
													WHERE display_name = '$memberToDelete'");
													
			$delUserInfo = mysqli_fetch_array($delUser);
			$delUserID = $delUserInfo['id'];
			if($delUserID == $Creator && !$isAdmin){
				print "Unable to remove item creator.  Please contact system administrator.";
				break;
			}
			$deleteProjUser = "DELETE FROM "."ItemUser 
								WHERE UserID = '$delUserID' AND ItemID = '$ItemID'";
								
			if(!mysqli_query($connection, $deleteProjUser)){
				die('Error: ' . mysqli_error($connection));
			}
		}
	}*/
}

/*if($ItemName != $token || !empty($_POST['completeItem']) || !empty($_POST['reactivateItem'])){

	//get the Item
	$item = mysqli_query($connection, "SELECT * FROM "."Item WHERE ItemName = '$token'");
	$itemInfo = mysqli_fetch_array($item);
	
	$ItemID = $itemInfo['ItemID'];
	$ItemName = $itemInfo['ItemName'];
	$Status = $itemInfo['Status'];
	$CompletionTime = $itemInfo['CompletionTime'];
	$Creator = $itemInfo['Creator'];
}

//get the ItemUser list for this item
$itemUsers = mysqli_query($connection, "SELECT * FROM "."ItemUser WHERE ItemID = '$ItemID'");
$projUsrTmp = mysqli_query($connection, "SELECT * FROM "."ItemUser WHERE ItemID = '$ItemID'");

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
}*/

//get the Item
$item = mysqli_query($connection, "SELECT * FROM "."Item WHERE ItemName = '$token'");
$itemInfo = mysqli_fetch_array($item);

$ItemID = $itemInfo['ItemID'];
$ItemName = $itemInfo['ItemName'];
$Status = $itemInfo['Status'];
$CompletionTime = $itemInfo['CompletionTime'];
$Creator = $itemInfo['Creator'];
$Type = $itemInfo['Type'];
$Priority = $itemInfo['Priority'];
$Description = $itemInfo['Description'];
$ContainerID = $itemInfo['ContainerID'];


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



echo"
	<form name='itemInfo' action='".$_SERVER['PHP_SELF']."?item=".$ItemName."' id='update' method='post'>
	<table class='admin'><tr><td>
	<h3>Item Information</h3>
	<p>
	<b>Name:</b> 
	<input type='text' name='itemName' form = 'update' value='".$ItemName."'/>
	</p>
	<p>
	
	";
	if($Type == 0){	
		echo"
		<a href='create_task.php?itemID=".$ItemID."'>Create Task</a><br />
		<a href='view_task.php?itemID=".$ItemID."'>View Task List</a>
		";
	}echo"


	<br>
	</p>
	<p>
	<p>
	<b>ID:</b> ".$ItemID ."
	</p>
	<p>
	<b>Priority: </b> <input type='text' name='priority' form = 'update' value='".$Priority."'/>
	</p>
	
	<p>
	<b>Status: </b>";
	if($Status == 0){
	echo"<select name='status' form = 'update'>
			<option selected value ='0'>Paused</option>
			<option value ='1'>In Progress</option>
			<option value ='2'>Completed</option>
			<option value ='3'>Accepted</option>
			<option value ='4'>Deleted</option>
			</select>";
	}elseif($Status == 1){
	echo"<select name='status' form = 'update'>
			<option value ='0'>Paused</option>
			<option selected value ='1'>In Progress</option>
			<option value ='2'>Completed</option>
			<option value ='3'>Accepted</option>
			<option value ='4'>Deleted</option>
			</select>";
	}elseif($Status == 2){
	echo"<select name='status' form = 'update'>
			<option value ='0'>Paused</option>
			<option value ='1'>In Progress</option>
			<option selected value ='2'>Completed</option>
			<option value ='3'>Accepted</option>
			<option value ='4'>Deleted</option>
			</select>";
	}elseif($Status == 3){
	echo"<select name='status' form = 'update'>
			<option value ='0'>Paused</option>
			<option value ='1'>In Progress</option>
			<option value ='2'>Completed</option>
			<option selected value ='3'>Accepted</option>
			<option value ='4'>Deleted</option>
			</select>";
	}elseif($Status == 4){
	echo"<select name='status' form = 'update'>
			<option value ='0'>Paused</option>
			<option value ='1'>In Progress</option>
			<option value ='2'>Completed</option>
			<option value ='3'>Accepted</option>
			<option selected value ='4'>Deleted</option>
			</select>";
	}
	echo"
	</p>
	
	";
	if($userRole == 1 || $isAdmin){
		//change container
		echo"
		<p>
		<b>Container: </b>";
		if($ContainerID == 0){
		echo"<select name='container' form = 'update'>
				<option selected value ='0'>Limbo</option>
				<option value ='1'>Product Backlog</option>
				<option value ='2'>Sprint Backlog</option>
				</select>";
		}elseif($ContainerID == 1){
			echo"<select name='container' form = 'update'>
				<option value ='0'>Limbo</option>
				<option selected value ='1'>Product Backlog</option>
				<option value ='2'>Sprint Backlog</option>
				</select>";
		}elseif($ContainerID == 2){
			echo"<select name='container' form = 'update'>
				<option value ='0'>Limbo</option>
				<option value ='1'>Product Backlog</option>
				<option selected value ='2'>Sprint Backlog</option>
				</select>";
		}
		
		echo"
		</p>";
	}
	
	echo"
	<p>
	<b>Estimated Completion Time: </b><input type='text' name='estComp' form = 'update' value='".$CompletionTime ."'/>
	</p>
	
	";
	/*<p>
	<b>Completion Time:</b> ";
	if($CompletionTime == NULL){
		echo "Item Active
		<br>
		Complete Item <input type='checkbox' name='completeItem' form = 'update' value='completeItem'>
		";
	}else{
		echo "$CompletionTime
		<br>
		Reactivate Item <input type='checkbox' name='reactivateItem' form = 'update' value='completeItem'>
		";
	}
	echo"
	
	</p>*/
	echo"
		

	<p><input type='submit' value='Update Item' form = 'update' class='submit'/></p>
	</form>

	</td><td>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	</td><td>
	
	<td><h3>Item Description</h3>
	<p>
	<textarea name='itemDescription' cols='20' required='required'>".$Description."</textarea>
	</p>
	</td>
	</tr>";
	/*
	//list all users in item
	$userNum =0;
	while($userList = mysqli_fetch_array($itemUsers)){
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
			<option value ='".$user['id']."1'>Item Manager</option>
			</select>";
		}else{
			echo"<select name='role[".$userNum."]' form = 'update'>
			<option selected value ='".$user['id']."0'>Item Manager</option>
			<option value ='".$user['id']."1'>Team Member</option>
			</select>";
		}
		echo"
		</p>
		<p>";
		$userNum++;
	}
	*/
	echo"
	</td><td>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	";
	if($Type == 0){
		echo"<h3>Tasks</h3>";
		
		//display all tasks for this item
		$qry = "SELECT * FROM "."Task WHERE ItemID = '$ItemID'";
		$tasks = mysqli_query($connection, $qry);
		while($row = mysqli_fetch_array($tasks)){
		
			echo"
			<form name='taskInfo' action='".$_SERVER['PHP_SELF']."?item=".$ItemName."' id='task' method='post'>
				<p>".$row['Description']."</p>
			";
			//task status 0 is
			if($row[CurrentUser] == NULL || "")
				echo"<p><input type='submit' value='Claim Task' form = 'task' class='submit'/></p>";
			elseif($loggeInUser->user_id == $row[CurrentUser])
				echo"<p><input type='submit' value='Unclaim Task' form = 'task' class='submit'/></p>
			</form>";

		}
	}
	echo"
	</td></tr>";
	
	
	
//ends main
echo"
</div>
<div id='bottom'></div>
<p>
	<b><a href='project.php?project=".$ProjectName."'>Back to Project Page</a></b>
	</p>

</div>
</body>
</html>";



?>
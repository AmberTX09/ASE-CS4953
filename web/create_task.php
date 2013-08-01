<?php
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");
$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");


$itemID = $_GET['itemID'];
$qry = "SELECT ItemName FROM "."Item WHERE "."Item.ItemID = '$itemID'";
$token = mysqli_query($connection, $qry);
$row = mysqli_fetch_row($token);
$itemName = $row[0];


$qry = "SELECT ProjectName FROM "."Project, "."Item WHERE "."Project.ProjectID = "."Item.ProjectID AND "."Item.ItemID = '$itemID'";
$token = mysqli_query($connection, $qry);
$row = mysqli_fetch_row($token);
$projectName = $row[0];

if(!empty($_POST)){
	
	$description = trim($_POST['taskDescription']);	
	//Retrieve the UserID of the manager creating the projecy.
	$userID = $loggedInUser->user_id;
	
	//Insert into the project table if name doesn't exists and not a blank username
	if($description != ''){
		$insertItem = "INSERT INTO "."Task(ItemID, Description, Status, Creator)
		VALUES('$itemID', '$description', '0', '$userID')";
		if(!mysqli_query($connection, $insertItem)){
			die('Error: ' . mysqli_error($connection));
		}
	}else{
		printf("May not add an empty description");
	}
}


echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<br/>
<div id='left-nav'>";
include("left-nav.php");

echo "
</div>
<div id='main'>

<h1> Create Task</h1>

<form name='newTask' action='".$_SERVER['PHP_SELF']."?itemID=".$itemID."' method='post'>

<p>
<label>Description:</label>
<textarea name='taskDescription' cols='20' required='required'></textarea>
</p>

<p>
<label>&nbsp;<br>
<input type='submit' value='Create'/>
</p>
</form>
<a href='project.php?project=".$projectName ."'><b>Return to project page</b></a><br>
<a href='item.php?item=".$itemName."'><b>Return to item page</b></a>

</div>
<div id='bottom'></div>
</div>
</body>
</html>";


?>
<?php
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");


$projectName = $_GET['create_item'];
$idName;
//$connection = mysqli_connect("localhost","root","root","2013teamb");
$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");

//Add new Project entry
if(!empty($_POST)){

	$errors = array();
	$ItemName = trim($_POST["itemName"]);
	$itemName = $ItemName;
	$TextArea = $_POST['itemDescription'];
	$TypeValue = $_POST['itemType'];
	$PriorityValue = $_POST['itemPriority'];
	$estCompletion = $_POST['completionHours'];

	$same = 0;

	//Grab Project Table.
	$projectGET = mysqli_query($connection, "SELECT * FROM "."Project WHERE ProjectName = '$projectName'");
	$projectInfo = mysqli_fetch_array($projectGET);
	$ProjectID = $projectInfo['ProjectID'];
	$ProjectName = $projectInfo['ProjectName'];
	
	$listOfItems = mysqli_query($connection, "SELECT * FROM "."Item WHERE ProjectID = '$ProjectID'");
	//Check to make sure projectName does not exist.
	while($row = mysqli_fetch_array($listOfItems)){
		$compName = $row['ItemName'];
		//Case insensitive check
		if(strcasecmp ($compName , $ItemName) == 0){
			$same = 1;
		}
	}
	
	
	//Retrieve the UserID of the manager creating the projecy.
	$userID = $loggedInUser->user_id;
	
	//Insert into the project table if name doesn't exists and not a blank username
	if($ItemName == ''){
		echo "Blank item names are not allowed";
	}elseif(!$same){
		$insertItem = "INSERT INTO "."Item(ItemName, Creator, ProjectID, Description, Type, Priority, CompletionTime)
		VALUES('$ItemName', '$userID', '$ProjectID', '$TextArea', '$TypeValue', '$PriorityValue', '$estCompletion')";
		if(!mysqli_query($connection, $insertItem)){
			die('Error: ' . mysqli_error($connection));
		}
		
		//GETTING INFO FOR HISTORY LOG
		$HistoryItem = mysqli_query($connection, "SELECT * FROM "."Item WHERE ItemName = '$ItemName' and ProjectID = '$ProjectID'");
		$HistoryItemArray = mysqli_fetch_array($HistoryItem);
		$HistoryDes = "Item Created!";
		$HistoryID = $HistoryItemArray ['ItemID'];
		
		//HISTORY LOG
		$itemLog = "INSERT INTO "."ItemLog(UserID, ItemID, description, ProjectID)
		VALUES('$userID', '$HistoryID', '$HistoryDes', '$ProjectID')";
		if(!mysqli_query($connection, $itemLog)){
			die('Error: ' . mysqli_error($connection));
		}


	}else{
		echo "Ite Name already exists";
	}
}

//Left Nav Bar
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h2>Create Item</h2>

<div id='left-nav'>";
include("left-nav.php");
echo "
</div>
<div id='main'>";
echo "
<div id='newProjBox'>
<form name='newProject' action='".$_SERVER['PHP_SELF']."?create_item=".$projectName."' method='post'>

<p>
<label>Item Name:</label>
<input type='text' name='itemName' />
</p>
<p>

<p>
<label>Description:</label>
<textarea name='itemDescription' cols='20' required='required'></textarea>
</p>

<p>
<label>Type:</label>
</p>
<p>
	<input type='radio' value='0' name='itemType' checked='checked'>Story<br>
	<input type='radio' value='1' name='itemType'>Chore<br>
	<input type='radio' value='2' name='itemType'>Bug<br>
</p>

<label>Priority:</label>
</p>
<p>
	<input type='radio' value='1' name='itemPriority' checked='checked'>1
	<input type='radio' value='2' name='itemPriority'>2
	<input type='radio' value='3' name='itemPriority'>3
	<input type='radio' value='4' name='itemPriority'>4
	<input type='radio' value='5' name='itemPriority'>5

</p>
<p>
	Estimated Completion time(in hours):
	<input type='text' name = 'completionHours' />


<p>
<label>&nbsp;<br>
<input type='submit' value='Create'/>
</p>

</form>
	<p>
	<a href='project.php?project=".$projectName."'>Project: ".$projectName."</a>
	</p>";
if (!empty($_POST['itemName'])){
		$itemName = trim($_POST["itemName"]);
		echo "<p><a href='item.php?item=".$itemName."'>Item: ".$itemName."</a></p>";
}
echo"
</div>

</div>
<div id='bottom'></div>
</div>
</body>
</html>";
?>

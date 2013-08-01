<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");

//$connection = mysqli_connect("localhost","root","root","2013teamb");
$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");


$token = $_GET['view_item'];

//get the item


$projectGET = mysqli_query($connection, "SELECT * FROM "."Project WHERE ProjectName = '$token'");
				
$projectInfo = mysqli_fetch_array($projectGET);
$ProjectID = $projectInfo['ProjectID'];
$ProjectName = $projectInfo['ProjectName'];

//$query = "SELECT p1.ProjectName FROM "."Project AS p1, "."ProjectUser AS p2 WHERE p1.ProjectID = p2.ProjectID AND p2.UserID = $loggedInUser->user_id";
$query = mysqli_query($connection, "SELECT * FROM "."Item WHERE ProjectID = '$ProjectID'"); 

//$result = mysqli_query($connection, $query);


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
<div id='main'>
<h2>View Items</h2>


<h1 style='font-size:xx-medium;'>List of all items assigned to ".$ProjectID.":</h1>
";
while($row = mysqli_fetch_array($query)){
	echo"
	<a href='item.php?item=".$row['ItemName']."'>Item Name:".$row['ItemName']." ID: ".$row['ItemID']."</a>
	<br>
	";
}
echo"

<a href='project.php?project=".$ProjectName."'>".$token."</a>
</div>


<div id='bottom'></div>
</div>
</body>
</html>";



?>
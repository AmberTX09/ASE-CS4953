<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");

$isAdmin = $loggedInUser->checkPermission(array(2));


//Forms posted
if(!empty($_POST))
{
	$deletions = $_POST['delete'];
	if ($deletion_count = deleteProject($deletions)){
		$successes[] = lang("ACCOUNT_DELETIONS_SUCCESSFUL", array($deletion_count));
	}
	else {
		$errors[] = lang("SQL_ERROR");
	}
}

//$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");
//$query = "SELECT p1.ProjectName FROM "."Project AS p1, "."ProjectUser AS p2 WHERE p1.ProjectID = p2.ProjectID AND p2.UserID = $loggedInUser->user_id";
//$query = "SELECT p1.ProjectName FROM "."Project AS p1, "."ProjectUser AS p2 WHERE p1.ProjectID = p2.ProjectID AND p2.UserID = $loggedInUser->user_id";

$result = fetchAllProjects();


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
<h2>View Projects</h2>


<h1 style='font-size:xx-medium;'>List of all projects you are apart of:</h1>
";


echo "
<form name='delete' action='".$_SERVER['PHP_SELF']."' method='post'>
<table class='admin'>
<tr>
<th>Delete</th><th>Project Name</th>
</tr>";

//Cycle through users
foreach ($result as $v1) {
	echo "
	<tr>";
		if ($loggedInUser->user_id == $v1['Creator'] || $isAdmin){
			echo "
			<td><input type='checkbox' name='delete[".$v1['ProjectID']."]' id='delete[".$v1['ProjectID']."]' value='".$v1['ProjectID']."'></td>";
		}
		echo "
	<td><a href='project.php?project=".$v1['ProjectName']."'>".$v1['ProjectName']."</a></td>
	<td>
	</tr>";
}

echo "
</table>
<input type='submit' name='Submit' value='Delete' />
</form>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>

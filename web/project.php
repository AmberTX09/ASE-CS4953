<?php 
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
$isAdmin = $loggedInUser->checkPermission(array(2));
$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");

/*------------------------------Project Implementation-----------------------------*/

//retrieves the project name from the URL.
$token = $_GET['project'];

require_once("models/header.php");

/*-----------------------------Left Nav bar-----------------------------*/
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<div id='left-nav'>";
include("left-nav.php");
/*-----------------------------Main Content-----------------------------*/
echo "
</div>
<div id='main'>";

echo "
<iframe src = 'projectiframe.php?token=".$token."' style = 'width:100%;height:600px;border:none;float:right;'>
</iframe>
";
echo "
<div id='bottom'></div>
</div>
</body>
</html>";
?>
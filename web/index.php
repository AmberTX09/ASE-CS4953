<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

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

<h1 style='font-size:xx-large;'>Hello welcome to our website Scrum Project Management.
We are currently under construction but we will be available to service you when the site is complete.</h1>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
<?php
require_once("rf/razorflow.php");
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");


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
";


$dataSource = new MySQLDataSource('2013teamb', '2013teamb', '29G8l!06J82ofpPw', 'devdb.fulgentcorp.com');

$dataSource->setSQLSource('Item');

// Create a chart to show aggregated sales by genre
$itemPriority = new ChartComponent();
$itemPriority->setCaption ("Item Priority");
$itemPriority->setDataSource($dataSource);
$itemPriority->setLabelExpression("Name", "Item.ItemName");
$itemPriority->addSeries("Priority", "Item.Priority");
$itemPriority->setOption('limit', 10);
Dashboard::addComponent($itemPriority);
Dashboard::Render();

echo "
</div>


<div id='bottom'></div>
</div>
</body>
</html>";



?>
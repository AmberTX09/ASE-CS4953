<?php
require "rf/razorflow.php";

$connection = mysqli_connect("devdb.fulgentcorp.com","2013teamb","29G8l!06J82ofpPw","2013teamb");
$token = $_GET['test_item'];


$project = mysqli_query($connection, "SELECT * FROM "."Project WHERE ProjectName = '$token'");
$projectInfo = mysqli_fetch_array($project);
$ProjectID = $projectInfo['ProjectID'];
$ProjectName = $projectInfo['ProjectName'];
$DateCreated = $projectInfo['DateCreated'];
$DateCompleted = $projectInfo['DateCompleted'];
$Creator = $projectInfo['Creator'];
$CurrentSprint = $projectInfo['CurrentSprint'];
$SprintDuration = $projectInfo['SprintDuration'];



$dataSource = new MySQLDataSource('2013teamb', '2013teamb', '29G8l!06J82ofpPw', 'devdb.fulgentcorp.com');
$dataSource->setSQLSource("Sprint");
$sprintRange = new ChartComponent();
$sprintRange->setCaption ("Sprint Metrics");
$sprintRange->setDataSource($dataSource);
$sprintRange->addCondition("Sprint.ProjectID", "==", "$ProjectID");
//$sprintRange->setLabelExpression("Sprint", "Sprint.SprintNum");
$sprintRange->setLabelExpression("Day", "Sprint.FinishTime", array(
														'timestampRange' => 'time',
														'timeUnit' => 'day',
														'customTimeUnitPath' => array('year', 'month', 'day'),
														'autoDrill' => true
));

//$sprintRange->setYAxis("Day");
$sprintRange->addSeries("Day", "SprintNum");
//$sprintRange->setOption('limit', 10);
Dashboard::addComponent($sprintRange);


$itemSource = new MySQLDataSource('2013teamb', '2013teamb', '29G8l!06J82ofpPw', 'devdb.fulgentcorp.com');
$itemSource->setSQLSource("Item");
$itemRange = new ChartComponent();
$itemRange->setCaption ("Item Metrics");
$itemRange->setDataSource($itemSource);
$itemRange->addCondition("Item.ProjectID", "==", "$ProjectID");
$itemRange->setLabelExpression("Name", "Item.ItemName");
$itemRange->addSeries("Item", "Item.Priority");
Dashboard::addComponent($itemRange);



Dashboard::Render();

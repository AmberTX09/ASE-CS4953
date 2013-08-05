<? php
require "rf/razorflow.php";


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
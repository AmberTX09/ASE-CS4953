<?php
require "rf/razorflow.php";

//$dataSource = RFUtil::getSampleDataSource();
//$dataSource = new MySQLDataSource('2013teamb', 'root', 'root', 'localhost');
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

/*// Create a chart to show aggregated sales by artist
$artistSales = new ChartComponent();
$artistSales->setCaption("Artist Sales");
$artistSales->setDataSource($dataSource);
$artistSales->setLabelExpression("Artist", "artist.Name");
$artistSales->addSeries("Sales", "track.UnitPrice * invoiceline.Quantity", array(
    'sort' => "DESC"
));
$artistSales->setOption('limit', 10);
Dashboard::addComponent($artistSales);

// Link the artist chart to artist sales
$itemPriority->autoLink($artistSales);

// Create a table component to show each sale.
$saleTable = new TableComponent();
$saleTable->setCaption("Sales Table");
$saleTable->setWidth(3);
$saleTable->setDataSource($dataSource);
$saleTable->addColumn("Track", "track.Name");
$saleTable->addColumn("Album", "album.Title");
$saleTable->addColumn("Sale Date", "invoice.InvoiceDate", array(
    'width'=>50
));
$saleTable->addColumn("Amount", "track.UnitPrice * invoiceLine.Quantity", array(
    'width'=>50,
    'textAlign'=>'right',
    'numberPrefix'=> '$'
));
Dashboard::addComponent($saleTable);

// Link the artist chart to the sales table
$artistSales->autoLink($saleTable);

// Create a Key Performance Indicators to measure the total sales last month
$saleKPI = new KPIComponent();
$saleKPI->setCaption("Last Month's sales");
$saleKPI->setDataSource($dataSource);
$saleKPI->setValueExpression("track.UnitPrice * invoiceLine.Quantity", array(
    'aggregate' => true,
    'aggregateFunction' => "SUM",
    'numberPrefix' => '$'
));
$saleKPI->setTimestampExpression("invoice.InvoiceDate", array(
    'timeUnit' =>'month'
));
Dashboard::addComponent($saleKPI);

// Link the artist chart to sales KPI
$artistSales->autoLink($saleKPI);

$yearlySalesGauge = new GaugeComponent();
$yearlySalesGauge->setCaption("This Year's sales");
$yearlySalesGauge->setPlaceholder("Please select an artist");
$yearlySalesGauge->setDataSource($dataSource);
$yearlySalesGauge->setValueExpression("invoiceLine.Quantity", array(
    'aggregate' => true,
    'aggregateFunction' => "SUM"
));
$yearlySalesGauge->setTimestampExpression("invoice.InvoiceDate", array(
    'timeUnit' => 'year'
));
$yearlySalesGauge->setKeyPoints(array(5, 10, 20, 30));
Dashboard::addComponent($yearlySalesGauge);

// Link the artist chart to the yearly sales gauge
$artistSales->autoLink($yearlySalesGauge);*/

Dashboard::Render();
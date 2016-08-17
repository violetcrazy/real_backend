<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <link rel="stylesheet" href="">
</head>
<body>

<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

/** Include PHPExcel_IOFactory */
require_once 'PHPExcel/IOFactory.php';


if (!file_exists("data_export.xlsx")) {
    exit("Chưa có file" . EOL);
}

echo date('H:i:s') , " Load from Excel2007 file" , EOL;
$callStartTime = microtime(true);

$objPHPExcel = PHPExcel_IOFactory::load("data_export.xlsx");

foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    echo 'Worksheet - ' , $worksheet->getTitle() , EOL;
    foreach ($worksheet->getRowIterator() as $row) {
        echo 'Dòng - ' , $row->getRowIndex() , EOL;
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
        foreach ($cellIterator as $cell) {
            if (!is_null($cell)) {
                echo '' , $cell->getCoordinate() , ' - ' , $cell->getCalculatedValue() , EOL;
            }
        }
    }
}
?>
</body>
</html>

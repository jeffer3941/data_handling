<?php 

require 'vendor/autoload.php';

use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\CharsetConverter;

//load the CSV document from a stream
$stream = fopen('assets/csv/questões_wordpress.csv', 'r');
$csv = Reader::createFromStream($stream);
$csv->setDelimiter(';');
$csv->setHeaderOffset(0);

$stmt = Statement::create();   
$records = $stmt->process($csv);

foreach ($records as $index=>$record) {    
    echo $record['title'] . PHP_EOL; 
}

?>
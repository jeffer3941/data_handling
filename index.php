<?php 

require 'vendor/autoload.php';

use emr\tratamentoCsv\CsvGenerator;
use League\Csv\Writer;

date_default_timezone_set('America/Sao_Paulo');

$csv = new CsvGenerator(); 
 
$csvData = $csv->processCsvData();

// try {
//     $csv_questions_download = Writer::createFromFileObject(new SplTempFileObject());
//     $csv_questions_download->insertOne(['identificador','enunciated','link_do_video','alternativa_a', 'alternativa_b','alternativa_c','alternativa_d','alternativa_e']);
//     $csv_questions_download ->insertAll($csvData);
//     $csv_questions_download->output('questions'.date('d-m-Y H:i:s', time()).'.csv');
// } catch (Exception | RuntimeException $e) {
//     echo $e->getMessage(), PHP_EOL;
// }

?>
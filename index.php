<?php 

require 'vendor/autoload.php';

use emr\tratamentoCsv\CsvGenerator;

$csv = new CsvGenerator();


// $csv_questions_download = Writer::createFromFileObject(new SplTempFileObject());
// $csv_questions_download->insertOne(['identificador', 'alternativa_a', 'alternativa_b','alternativa_c','alternativa_d','alternativa_e','enunciated']);
// $csv_questions_download->output('questions.csv');
// exit();
// echo 'error: ', json_last_error_msg(). PHP_EOL;
?>
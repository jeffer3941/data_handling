<?php 

namespace emr\tratamentoCsv;

use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class CsvGenerator {
    public function __construct() {
        $this->processCsvData();
    }

    private function processCsvData()
    {
        $stream = fopen('assets/csv/questões_wordpress.csv', 'r');
        $csv = Reader::createFromStream($stream);

        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $stmt = Statement::create();   
        $records = $stmt->process($csv);

        $answer_data = array();

        foreach ($records as $index=>$record) {   
            array_push($answer_data,str_replace(array('<p>','</p>','*'),'',$record['answer_data']));
        }

        $fixed_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {      
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        },$answer_data);

        $alternatives = json_decode(json_encode(unserialize($fixed_data)));


        foreach($alternatives as $alternative){
            if($alternative->_answer == '') {
                continue;
            }
            echo $alternative->_answer.PHP_EOL;
        }

    }
    
}

?>
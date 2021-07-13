<?php 

namespace emr\tratamentoCsv;

use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\Statement;
 

class CsvGenerator {
 
    public function processCsvData() : array 
    {
        $stream = fopen('assets/csv/questões_wordpress.csv', 'r');
        $csv = Reader::createFromStream($stream);

        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $stmt = Statement::create();   
        $records = $stmt->process($csv);

        $answer_data = array(
            'identificador' => '',
            'enunciated' => '',
            'video' => '',  
            'alternative_a' =>[ 'a','b','c','d','e']      
        );

        foreach ($records as $index=>$record) {  

            $answer_data['identificador'] = $record['title'];
            $answer_data['enunciated']    = $record['question'];
            $answer_data['video']         = $record['correct_msg'];

            // $fixed_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {      
            //     return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
            // },str_replace(array('<p>','</p>','*'),'',$record['answer_data']));

            // $alternatives = json_decode(json_encode(unserialize($fixed_data)));
 
            // foreach($alternatives as $alternative){
            //         // if($alternative->_answer == '') {
            //         //     continue;
            //         // }
            //         // echo $alternative->_answer.PHP_EOL;
            //     @array_push($answer_data, $alternative->_answer);
            // }
        }
 
        return $answer_data;        
    }
 
    
}

?>
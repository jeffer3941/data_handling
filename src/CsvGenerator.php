<?php 

namespace emr\tratamentoCsv;

use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\Statement;
class CsvGenerator
{ 
    public function processCsvData( ):array
    {
        $stream = fopen('assets/csv/questões_wordpress.csv', 'r');
        $csv = Reader::createFromStream($stream);

        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $stmt = Statement::create();
        $records = $stmt->process($csv);


        $questions_data = array();

        foreach ($records as $index => $record) {   
            $alternatives = array();
            $answer_data = array(
                'identificador' => '',
                'enunciated'    => '',
                'video'         => '',
                'alternative_a' => '',
                'alternative_b' => '',
                'alternative_c' => '',
                'alternative_d' => '',
                'alternative_e' => '',
                'correct'       => '',                   
            );

            $answer_data['identificador'] = strip_tags($record['title']);
            $answer_data['enunciated']    = strip_tags($record['question']);
            $answer_data['video']         = $record['correct_msg'];
            
            $fixed_data = $this->fixedDataSeralized($record['answer_data']);
 
            $alternatives = json_decode(json_encode(@unserialize($fixed_data)),true);  
             
            if (gettype($alternatives) == 'boolean') {
                continue;
            }          
 
            if ($alternatives !== false) {
                $alternative_title_collum = ['alternative_a','alternative_b','alternative_c','alternative_d','alternative_e'];
                $alternative_correct_value = ['A','B','C','D','E'];

                foreach ($alternatives as $index => $alternative){ 
                    if($alternative['_correct'] == 1){
                        $answer_data['correct'] = $alternative_correct_value[$index];
                    }
                    @$answer_data[$alternative_title_collum[$index]] = strip_tags($alternative['_answer']);                  
                }
            }     
            array_push($questions_data,$answer_data);      
        }
        return $questions_data;            
    }  
    public function fixedDataSeralized(string $data):string
    {
        $fixed_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {      
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        },str_replace('*','',$data));
        
        return $fixed_data;
    }
}
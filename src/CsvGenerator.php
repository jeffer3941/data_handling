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
        $pattern_vimeo = '/vimeo/';
        $pattern_vdo   = '/vdo id/';
        foreach ($records as $index => $record) {   
            $alternatives = array();
            $answer_data = array(
                'identificador' => '',
                'enunciated'    => '',
                'video'         => '',
                'comment'       => '',
                'alternative_a' => '',
                'alternative_b' => '',
                'alternative_c' => '',
                'alternative_d' => '',
                'alternative_e' => '',
                'correct'       => '',                   
            );
            $answer_data['identificador'] = strip_tags($record['title']);
            $answer_data['enunciated']    = strip_tags($record['question']);
            $answer_data['video']         = strip_tags($record['correct_msg']); 
            $length                       = strpos($answer_data['video'], ']');
            $comment                      = substr($answer_data['video'], $length+1, 2000);
            $video                        = $this->extractLink(substr($answer_data['video'], 0, $length+1));
            
            // if(preg_match($pattern_vimeo,$video)) {
            //     continue;
            // }
            if(preg_match($pattern_vdo,$video) || $video == null || $video == '') {
                continue;
            }
            $answer_data['comment']       = $comment;
            $answer_data['video']         = $video;

            $fixed_data = $this->fixedDataSeralized($record['answer_data']);
 
            $alternatives = json_decode(json_encode(@unserialize($fixed_data)),true);          
 
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
    public function extractLink(string $video):string
    {
        $video_extract = str_replace(['[su_vimeo url="','[su_vimeo_button url="',
        '" width="640" height="360" responsive="yes" autoplay="no" mute="no" dnt="no" title="" texttrack="" class=""]'],
        '',$video);
        return $video_extract;
    }
}
<?php
namespace emr\tratamentoCsv;

use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\Statement;
class CsvGenerator
{ 
    public function processCsvData( ):array
    {
        $stream = fopen('assets/csv/questõesWPcomVimeoFilter.csv', 'r');
        $csv = Reader::createFromStream($stream);

        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $stmt = Statement::create();
        $records = $stmt->process($csv);


        $questions_data = array();
        $pattern_vimeo = '/vimeo/';
        $pattern_vdo   = '/vdo id/';
        define('PROVE','/UNICAMPSPSP/');

        foreach ($records as $index => $record) {   
            $alternatives = array();
            $answer_data = array(
                'prova'         => '',
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
            
            $answer_data['prova']         = $this->implodeProff(strip_tags($record['title']));
            $answer_data['identificador'] = strip_tags($record['title']);
            $answer_data['enunciated']    = strip_tags($record['question']);
            $answer_data['video']         = strip_tags($record['correct_msg']); 
            $length                       = strpos($answer_data['video'], ']');
            $comment                      = substr($answer_data['video'], $length+1, 2000);
            $video                        = $this->extractLink(substr($answer_data['video'], 0, $length+1));
            
            // if(preg_match($pattern_vimeo,$video) || $video == null || $video == '') {
            //     continue;
            // }
            // if(preg_match($pattern_vdo,$video) || $video == null || $video == '') {
            //     continue;
            // }

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

    public function processCvsDataAffeterSeralized():array 
    {
    
        $stream = fopen('assets/csv/questõesWPcomVimeoFilter.csv', 'r');
        $csv = Reader::createFromStream($stream);

        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        $stmt = Statement::create();
        $records = $stmt->process($csv);


        $questions_data = array();
        $pattern_vimeo = '/vimeo/';
        $pattern_vdo   = '/vdo id/';
        $characters = "\n\r\t\v\0";
        foreach ($records as $index => $record) {   
            $alternatives = array();
            $answer_data = array(
                'prova'         => '',
                'order'         => '',
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
            // $answer_data['prova']         =  @$this->implodeProff($record['identificador']);
            $answer_data['order']         =  @$this->extractOrder($record['identificador']);
            // $answer_data['identificador'] = trim($record['identificador'],$characters);
            // $answer_data['enunciated']    = ltrim($record['enunciated'],$characters);
            // $answer_data['video']         = trim($record['link_do_video'],$characters); 
            // $answer_data['comment']       = trim($record['comment'],$characters);
            // $answer_data['alternative_a'] = trim($record['alternativa_a'],$characters);
            // $answer_data['alternative_b'] = trim($record['alternativa_b'],$characters);
            // $answer_data['alternative_c'] = trim($record['alternativa_c'],$characters);
            // $answer_data['alternative_d'] = trim($record['alternativa_d'],$characters);
            // $answer_data['alternative_e'] = trim($record['alternativa_e'],$characters);
            // $answer_data['correct'] = trim($record['correct'],$characters);    
                    
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

    public function implodeProff(string $identifier):string
    {
        $groupProof  = array();

        $stringCorrentArray = str_split($identifier);
        $indexStartYear = array_search('2',$stringCorrentArray);

        $year = $stringCorrentArray[$indexStartYear].$stringCorrentArray[$indexStartYear+1].$stringCorrentArray[$indexStartYear+2].$stringCorrentArray[$indexStartYear+3];
        $state = $stringCorrentArray[$indexStartYear-2].$stringCorrentArray[$indexStartYear-1];

        array_pop($stringCorrentArray);
        array_pop($stringCorrentArray);
        if(array_search('Q',$stringCorrentArray)!==false){
            array_pop($stringCorrentArray);
        }

        $stringCorrentArray = explode($state.$year,implode('',$stringCorrentArray));
        
        $evaluation = $stringCorrentArray[0];
        $type = $stringCorrentArray[1];

        array_push($groupProof,$evaluation,$state,$year,$type);
        $proff = implode(' ',$groupProof);

        return $proff;
    }

    public function extractOrder(string $identifier):string
    {
        $length = strpos($identifier, 'Q');
        $order = substr($identifier, $length+1, 2000);
        return $order;
    }
}
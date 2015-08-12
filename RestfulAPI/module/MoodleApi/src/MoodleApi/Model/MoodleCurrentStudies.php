<?php
namespace MoodleApi\Model;
use MoodleApi\Model\MoodleSchool;
use MoodleApi\Utilities\Common;
class MoodleCurrentStudies  extends Common
{
	
    
    public function get($data)
    {
    	//var_dump($data);
    	//for($i=0;count($data)>$i;$i++){
    	$currentStudy= new \stdClass();
    		if(array_key_exists ( 'currentStudies' , $data )){
    			$studies_tmp=$data['currentStudies'];
    			
    			if (trim($studies_tmp)==""){
    				return array();
    			}
    			
    			$studies_tmp=$this->createTableFromCompundField($studies_tmp);
    			if (count($studies_tmp)>0){
    				$currentStudy->level=$studies_tmp[0][0];
    				$currentStudy->grade=$studies_tmp[0][1];
    				$currentStudy->period=$studies_tmp[0][2];
    			}
    			
    				//array_push($studies, new MoodleSchool($study[0], $study[1]));
    			
    		}
    		
    	//}
    	return $currentStudy;
    }
}
<?php
namespace MoodleApi\Model;
use MoodleApi\Model\MoodleSchool;
use MoodleApi\Utilities\Common;
class MoodleStudies  extends Common
{
    
    public function get($data)
    {
    	$studies=array();
    	//var_dump($data);
    	//for($i=0;count($data)>$i;$i++){
    		if(array_key_exists ( 'studies' , $data )){
    			$studies_tmp=$data['studies'];
    			
    			if (trim($studies_tmp)==""){
    				return array();
    			}
    			$studies_tmp=$this->createTableFromCompundField($studies_tmp);
    			foreach ($studies_tmp as $study){
    				if ($study===""){
    					continue;
    				}
    				array_push($studies, new MoodleSchool($study[0], $study[1]));
    			}
    		}
    		
    	//}
    	return $studies;
    }
}
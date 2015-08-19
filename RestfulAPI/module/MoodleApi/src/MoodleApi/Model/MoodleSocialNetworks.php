<?php
namespace MoodleApi\Model;
use MoodleApi\Model\MoodleSocialNetwork;
use MoodleApi\Utilities\Common;
class MoodleSocialNetworks extends Common
{
	public $socialNetworks;
    
    
    public function get($data)
    {
    	$networks=array();
    	//for($i=0;count($data)>$i;$i++){
    		if(array_key_exists ( 'socialNetworks' , $data )){
    			$networks_temp=$data['socialNetworks'];
    			if (trim($networks_temp)==""){
    				return array();
    			}
    			$networks_temp=$this->createTableFromCompundField($networks_temp);
    			foreach ($networks_temp as $network){
    				if ($network===""){
    					continue;
    				}
    				array_push($networks, new MoodleSocialNetwork($network[0], $network[1]));
    			}
    			
    		}
    		return $networks;
    		
    	//}
    }
}
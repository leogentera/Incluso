<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;

class UpdateUserProfileController extends AbstractRestfulJsonController {
    
    private $token = "";
    private $function = "core_user_update_users";
	
    // Action used for POST requests
    public function create($data)
    {
    	
//     	$url = $this->getConfig()['MOODLE_API_URL'].'&field=id&values[0]=%s';
//     	$url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $data['id']);
    	 
//     	$response = file_get_contents($url);
//     	$json = json_decode($response,true);
    	 
//     	if (strpos($response, "exception") !== false)
//     	{
//     		return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
//     	}
//     	else
//     	{
//     		if(count($json)==0){
//     			return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
//     		}
    			
//     		$id=$json[0]['id'];
    		
    		
	    	$url = $this->getConfig()['MOODLE_API_URL']."&users[0][id]=%s";
	    	$url = sprintf($url, $this->getToken(), "core_user_update_users", $data['id']);
	 	    
	    	//$url.=createURLParms($data, '&users[0][%s]=%s', 'country' );
	    	$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'firstname' );
	    	$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'lastname' );
	    	
	    	$address=$this->createTableRow($data, "", 'street', 'num_ext', 'num_int', 'colony' );
	    	if (trim($address)!=""){
	    		$url.=sprintf('&users[0][customfields][0][type]=%s&users[0][customfields][0][value]=%s',
	    				"address",$address);
	    	}
	    	$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'city' );
	    	$url.=$this->createURLParms($data, '&users[0][%s]=%s', 'country' );
	    	
	    	$url.=$this->createURLParms($data, '&users[0][customfields][1][type]=%s&users[0][customfields][1][value]=%s', 'town' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][2][type]=%s&users[0][customfields][2][value]=%s', 'state' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][3][type]=%s&users[0][customfields][3][value]=%s', 'postalCode' );
	    	
	    	$studies=$this->createTableRows($data,  'school', 'levelOfStudies' );
	    	
	    	if (trim($studies)!=""){
	    		$url.=sprintf('&users[0][customfields][4][type]=%s&users[0][customfields][4][value]=%s',
	    				"studies",$studies);
	    	}
	    	
	    	$familiaCompartamos=$this->createTableRows($data,  'idClient', 'relativeName', 'relationship' );
	    	if (trim($familiaCompartamos)!=""){
	    		$url.=sprintf('&users[0][customfields][5][type]=%s&users[0][customfields][5][value]=%s',
	    				"familiaCompartamos",$familiaCompartamos);
	    	}
	    	
	    	$phones=$this->createTableRows($data,  'phone' );
	    	if (trim($phones)!=""){
	    		$url.=sprintf('&users[0][customfields][6][type]=%s&users[0][customfields][6][value]=%s',
	    				"phones",$phones);
	    	}
	    	
	    	$url.=$this->createURLParms($data, '&users[0][customfields][7][type]=%s&users[0][customfields][7][value]=%s', 'stage' );
	    	
	    	$socialNetworks=$this->createTableRows($data,  'socialNetwork', 'socialNetworkId' );
	    	
	    	if (trim($socialNetworks)!=""){
	    		$url.=sprintf('&users[0][customfields][8][type]=%s&users[0][customfields][8][value]=%s',
	    				"socialNetworks",$socialNetworks);
	    	}
	    	
	    	
	    	//$url = sprintf($url, $this->getToken(), $this->function);
	    	
	    	$response = file_get_contents($url);
	    	if ($response=="null"){
	    		return  new JsonModel(array());
	    	}
	    	else{
	    		$json = json_decode($response,true);
	    	}
	    	
	    		
	    		 
	    		if (strpos($response, "error") !== false)
	    		{
	    			if (strpos($json["errorcode"], "authpluginnotfound") !== false){
	    				$message = 'El usuario ingresado no es válido';
	    			}
	    			else{
	    				return new JsonModel($this->throwJSONError());
	    			}
	    			return new JsonModel($this->throwJSONError($message));
	    		}
	    	
	    	
	    	
	    	
	    //}

    }
    
    function createURLParms($array, $format, $key ){
    	if(array_key_exists ( $key , $array )){
    		return sprintf($format, $key, urlencode($array[$key]));
    		
    	}
    	return "";
    	
    }
    
    function createTableRow($array, $sufix, ...$keys ){
    	$result="";
    	$i=0;
    	foreach ($keys as $key){
    		if($i>0){
    			$result.="\t";
    		}
    		if(array_key_exists ( $key.$sufix , $array )){
    			$result.=$array[$key.$sufix];
    			$i++;
    		}
    	}
    	$result= urlencode($result);
    	
    	return $result;
    	 
    }
    
    function createTableRows($array, ...$keys ){
    	$result="";
    	$i=0;
    	//foreach ($keys as $key){
    	
    		while(array_key_exists ( $keys[0].$i , $array )){
    			if($i>0){
    				$result.=urlencode("\n");
    			}
    			$result.=$this->createTableRow($array,$i, ...$keys );
    			
    			$i++;
    		}
    	//}
    		
    	return $result;
    
    }
}




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
	    	

	    	$attributesAndQualities=$this->createTableRows($data,  'attributesAndQualities' );
	    	if (trim($attributesAndQualities)!=""){
	    		$url.=sprintf('&users[0][customfields][9][type]=%s&users[0][customfields][9][value]=%s',
	    				"attributesAndQualities",$attributesAndQualities);
	    	}
	    	
	    	$dreamsToBe=$this->createTableRows($data,  'dreamsToBe' );
	    	if (trim($dreamsToBe)!=""){
	    		$url.=sprintf('&users[0][customfields][10][type]=%s&users[0][customfields][10][value]=%s',
	    				"dreamsToBe",$dreamsToBe);
	    	}
	    	
	    	$dreamsToHave=$this->createTableRows($data,  'dreamsToHave' );
	    	if (trim($dreamsToHave)!=""){
	    		$url.=sprintf('&users[0][customfields][11][type]=%s&users[0][customfields][11][value]=%s',
	    				"dreamsToHave",$dreamsToHave);
	    	}
	    	
	    	$dreamsToDo=$this->createTableRows($data,  'dreamsToDo' );
	    	if (trim($dreamsToDo)!=""){
	    		$url.=sprintf('&users[0][customfields][12][type]=%s&users[0][customfields][12][value]=%s',
	    				"dreamsToDo",$dreamsToDo);
	    	}
	    	
	    	$likesAndPreferences=$this->createTableRows($data,  'likesAndPreferences' );
	    	if (trim($likesAndPreferences)!=""){
	    		$url.=sprintf('&users[0][customfields][13][type]=%s&users[0][customfields][13][value]=%s',
	    				"likesAndPreferences",$likesAndPreferences);
	    	}
	    	
	    	
	    	//
	    	$url.=$this->createURLParms($data, '&users[0][customfields][14][type]=%s&users[0][customfields][14][value]=%s', 'showMyInformation' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][15][type]=%s&users[0][customfields][15][value]=%s', 'showAttributesAndQualities' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][16][type]=%s&users[0][customfields][16][value]=%s', 'showLikesAndPreferences' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][17][type]=%s&users[0][customfields][17][value]=%s', 'showBadgesEarned' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][18][type]=%s&users[0][customfields][18][value]=%s', 'showStrengths' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][19][type]=%s&users[0][customfields][19][value]=%s', 'showRecomendedBachelorDegrees' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][20][type]=%s&users[0][customfields][20][value]=%s', 'showMyDreams' );
	    	var_dump($data);
	    	$url.=$this->createURLParms($data, '&users[0][customfields][21][type]=%s&users[0][customfields][21][value]=%s', 'alias' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][22][type]=%s&users[0][customfields][22][value]=%s', 'termsAndConditions' );
	    	$url.=$this->createURLParms($data, '&users[0][customfields][22][type]=%s&users[0][customfields][22][value]=%s', 'informationUsage' );
	    	var_dump($url);
	    	
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




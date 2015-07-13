<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;
class AddStarsController extends AbstractRestfulJsonController {
	
    private $token = "";
    private $function = "core_user_update_users";
	
    // Action used for POST requests
    public function create($data)
    {
    	
    	$url = $this->getConfig()['MOODLE_API_URL'].'&field=id&values[0]=%s';
    	
    	
    	$url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $data['id']);
    	
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	 
    	if (strpos($response, "exception") !== false)
    	{
    		return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
    	}
    	else
    	{
    		
    	$customFields=array();
        for($i=0;count($json[0]['customfields'])>$i;$i++){
        	$customFields[$json[0]['customfields'][$i]['name']]=$json[0]['customfields'][$i]['value'];
        }
        
        $currentStars=$data['stars'];
        
        if (key_exists('stars', $customFields)){
        	$currentStars+=$customFields['stars'];
        }
        
    		$url = $this->getConfig()['MOODLE_API_URL']."&users[0][id]=%s";
    		$url = sprintf($url, $this->getToken(), "core_user_update_users", $data['id']);
    		
    		$url.=sprintf('&users[0][customfields][0][type]=%s&users[0][customfields][0][value]=%s',
    				"stars",$currentStars);
    		
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
    	}
    	
    	
    }
    
    
}




?>

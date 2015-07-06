<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;

class RegisterController extends AbstractRestfulJsonController {
    
    private $token = "";
    private $function = "core_user_create_users";
	
    // Action used for POST requests
    public function create($data)
    {
    	$url = $this->getConfig()['MOODLE_API_URL'].
    			'&users[0][username]=%s'.
    			'&users[0][password]=%s'.
    			'&users[0][firstname]=%s'.
    			'&users[0][lastname]=%s'.
    			'&users[0][email]=%s'.
    			'&users[0][city]=%s'.
    			'&users[0][country]=%s'.
    			'&users[0][customfields][0][type]=secretanswer&users[0][customfields][0][value]=%s'.
    			'&users[0][customfields][1][type]=secretquestion&users[0][customfields][1][value]=%s'.
    			'&users[0][customfields][2][type]=birthday&users[0][customfields][2][value]=%s'.
    			'&users[0][customfields][3][type]=gender&users[0][customfields][3][value]=%s';
    	
    	$dateinMilis= $data['birthday'];
//     	$dateinMilis=strtotime($data['birthday']) * 1000;
//     	$dateinMilis+=3600000;
    	
    	$url = sprintf($url, $this->getToken(), $this->function, 
    			$data['username'], 
    			urlencode($data['password']),
    			"", 
    			"",  
    			$data['email'],
    			urlencode($data['city']), urlencode($data['country']), 
    			urlencode($data['secretanswer']), 
    			urlencode($data['secretquestion']), 
    			$data['birthday'], 
    			$data['gender']);
    	
    	
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	if (strpos($response, "error") !== false)
    	{
    		
    		
    		
    		if ($json["debuginfo"]=="Email address is invalid"){
    			//$associativeArray ['messageerror'] = ;
    			$message='El email no es válido';
    		}
    		else if (strpos($json["debuginfo"], "Username already exists") !== false){
    			$message = 'El usuario que ingresaste ya esta registrado';
    		}
    		else if (strpos($json["debuginfo"], "Email address already exists") !== false){
    			$message = 'El email que ingresaste ya esta registrado';
    		}
    		else{
    			return new JsonModel($this->throwJSONError());
    		}
    		
    		return new JsonModel($this->throwJSONError($message));
    	}
    		return  new JsonModel(array());
    }
    
    
}



?>

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
    	'&users[0][username]=%s&users[0][password]=%s&users[0][firstname]=%s&users[0][lastname]=%s&users[0][email]=%s&users[0][auth]=%s&users[0][idnumber]=%s&users[0][lang]=%s&users[0][calendartype]=%s&users[0][city]=%s&users[0][country]=%s';
    	
    	//$url = sprintf($url, $this->getToken(), $this->function, $data['username'], $data['password'], $data['firstname'], $data['lastname'], $data['email'], $data['auth'], $data['idnumber'], $data['lang'], $data['calendartype']);
    	$url = sprintf($url, $this->getToken(), $this->function, $data['username'], $data['password'], "", "",  $data['email'], $data['auth'], $data['idnumber'], $data['lang'], $data['calendartype'], $data['city'], $data['country']);
    	//var_dump($url);
    	
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	if (strpos($response, "error") !== false)
    	{
    		$this->getResponse()->setStatusCode(401);
    		//return new JsonModel ($json);
    		// 	    		// Error
    		// 	    		$error = new MoodleException();
    		// 	    		$error->exchangeArray($json);
    		// 	    		array_push($courses, $error);
    	}
    	return new JsonModel($json);
    }
    
    
}





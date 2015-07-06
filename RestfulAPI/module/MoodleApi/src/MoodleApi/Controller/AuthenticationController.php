<?php

namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;

/**
 * AuthenticationController
 *
 * @author
 *
 * @version
 *
 */
class AuthenticationController extends AbstractRestfulJsonController {
	/**
	 * The default action - show the home page
	 */
    // Action used for POST requests
    public function create($data)
    {
    	//var_dump($data);
//     	try {
	    	$url = $this->getConfig()['TOKEN_GENERATION_URL'];
	    	$url = sprintf($url, $data['username'], $data['password'], $this->getConfig()['MOODLE_SERVICE_NAME']);

	    	//var_dump($url);
	    	$response = file_get_contents($url);
	    	$json = json_decode($response,true);
	    	if (strpos($response, "error") !== false)
	    	{
	    		$this->getResponse()->setStatusCode(401);
	    		return new JsonModel ([]);
// 	    		// Error
// 	    		$error = new MoodleException();
// 	    		$error->exchangeArray($json);
// 	    		array_push($courses, $error);
	    	}
	    	//setcookie('MOODLE_TOKEN', $json['token'], time() + 3600, '/',null, false); //the true indicates to store only if there´s a secure connection
	    	
//     	} catch (Exception $e) {
//     		echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
//     	}
    	
    	return new JsonModel($json);
    }
    public function getList()
    {
        return new JsonModel(array('data' => array('id'=> 3, 'name' => 'New Album', 'band' => 'New Band')));
    }
    
    private function generateToken() {
    }
    
    public function getToken() {
    	$token = '';
    	$request = $this->getRequest();
    	if ($this->hasToken()) {
    		$token = $request->getCookie()->MOODLE_TOKEN;
    	} else {
    		$token = $this->generateToken();
    	}
    	return $token;
    }
}
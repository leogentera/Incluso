<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleUser;
use MoodleApi\Model\MoodleException;
use MoodleApi\Model\MoodleApi\Model;

class UserProfileController extends AbstractRestfulJsonController{
	

	private $token = "";
	private $function = "core_user_view_user_profile";
	
// 	private $config;
// 	private function getConfig()
// 	{
// 		if ($this->config == null) {
// 			$this->config = $this->getServiceLocator()->get('config');
// 		}
// 		return $this->config;
// 	}
	
	

	// Action used for GET requests without resource Id
	public function getList()
	{
		$url = $this->getConfig()['MOODLE_API_URL'];
		$url = sprintf($url, $this->getToken(), $this->function);
		
		$response = file_get_contents($url);
		$json = json_decode($response,true);
	
		$courses= array();
// 		if (strpos($response, "exception") !== false)
// 		{
// 			// Error
// 			$error = new MoodleException();
// 			$error->exchangeArray($json);
// 			array_push($courses, $error);
// 		}
// 		else
// 		{
// 			// Good
// 			foreach ($json as $res) {
// 				$course = new MoodleUser();
// 				$course->exchangeArray($res);
// 				array_push($courses, $course);
// 			}
// 		}
		return new JsonModel($courses);
	}
	
	// Action used for GET requests with resource Id
	public function get($id)
	{
        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s';
        $url = sprintf($url, $this->getToken(), $this->function, $id);

        $response = file_get_contents($url);
        var_dump($url);
        $json = json_decode($response,true);

        $users= array();
        
		if (strpos($response, "exception") !== false) 
        {
            // Error
            $error = new MoodleException();
            $error->exchangeArray($json);
            array_push($users, $error);
        }
        else
        {
            // Good
            foreach ($json as $res) {
                $user = new MoodleUser();
    			$user->exchangeArray($res);
                array_push($users, $user);
            }

        }
        return new JsonModel($users);
    }

    
    
    private function hasToken() {
    	$request = $this->getRequest();
    	if (isset($request->getCookie()->MOODLE_TOKEN)) {
    		return true;
    	}else{
    		return false;
    	}
    }

    private function generateToken() {
    	$url = $this->getConfig()['TOKEN_GENERATION_URL'];
    	$url = sprintf($url, 'admin', 'Admin123!', $this->getConfig()['MOODLE_SERVICE_NAME']);
    
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    
    	setcookie('MOODLE_TOKEN', $json['token'], time() + 3600, '/',null, false); //the true indicates to store only if there´s a secure connection
    
    	return $json['token'];
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
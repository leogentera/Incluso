<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleUser;
use MoodleApi\Model\MoodleException;
use MoodleApi\Model\MoodleApi\Model;

class UserController extends AbstractRestfulJsonController{
	

	private $token = "";
	private $function = "core_user_get_users_by_field";
	
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
        $url = $this->getConfig()['MOODLE_API_URL'].'&field=id&values[0]=%s';
        $url = sprintf($url, $this->getToken(), $this->function, $id);

        $response = file_get_contents($url);
        var_dump($response);
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

    
    
}
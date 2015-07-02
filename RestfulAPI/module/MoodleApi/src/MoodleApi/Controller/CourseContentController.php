<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleCourseContent;
use MoodleApi\Model\MoodleException;

class CourseContentController extends AbstractRestfulJsonController {
    
    private $token = "";
    private $function = "core_course_get_contents";
	
//     private $config;
//     private function getConfig() {
//     	if ($this->config == null) {
//     		$this->config = $this->getServiceLocator()->get('config');
//     	}
//     	return $this->config;
//     }
    
    //test
    // Action used for GET requests without resource Id
    public function getList()
    {    	
        $url = $this->getConfig()['MOODLE_API_URL'];
        $url = sprintf($url, $this->getToken(), $this->function);
       	var_dump($url);
        $response = file_get_contents($url);
        $json = json_decode($response,true);
        
        $courses= array();
        if (strpos($response, "exception") !== false) 
        {
            // Error
            $error = new MoodleException();
            $error->exchangeArray($json);
            array_push($courses, $error);
        }
        else
        {
            // Good
            foreach ($json as $res) {
                $course = new MoodleCourseContent();
                $course->exchangeArray($res);
                array_push($courses, $course);
            }
        }
        return new JsonModel($courses);
    }

    // Action used for GET requests with resource Id
    public function get($id)
    {
    	$url = $this->getConfig()['MOODLE_API_URL'].'&courseid=%d';
    	$url = sprintf($url, $this->getToken(), $this->function, $id);
		
        $response = file_get_contents($url);
        $json = json_decode($response,true);

        $courses= array();
        if (strpos($response, "exception") !== false) 
        {
            // Error
            $error = new MoodleException();
            $error->exchangeArray($json);
            array_push($courses, $error);
        }
        else
        {
            // Good
            foreach ($json as $res) {
                $course = new MoodleCourseContent();
                $course->exchangeArray($res);
                array_push($courses, $course);
            }
        }
        return new JsonModel($courses);
    }

    // Action used for POST requests
    public function create($data)
    {
        return new JsonModel(array('data' => array('id'=> 3, 'name' => 'New Album', 'band' => 'New Band')));
    }

    // Action used for PUT requests
    public function update($id, $data)
    {
        return new JsonModel(array('data' => array('id'=> 3, 'name' => 'Updated Album', 'band' => 'Updated Band')));
    }

    // Action used for DELETE requests
    public function delete($id)
    {
        return new JsonModel(array('data' => 'album id 3 deleted'));
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
        $url = sprintf($url, 'test', 'Test123!', $this->getConfig()['MOODLE_SERVICE_NAME']);
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





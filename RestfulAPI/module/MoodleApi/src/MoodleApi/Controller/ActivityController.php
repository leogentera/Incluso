<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

use MoodleApi\Model\MoodleAssigment;
use MoodleApi\Model\MoodleForum;
use MoodleApi\Model\MoodleLabel;
use MoodleApi\Model\MoodlePage;
use MoodleApi\Model\MoodleQuiz;
use MoodleApi\Model\MoodleResource;
use MoodleApi\Model\MoodleUrl;

class ActivityController extends AbstractRestfulJsonController {
    
    private $token = "";

    public function get($coursemoduleid){

        $activity = $this->getIdAndTypeOfActivity($coursemoduleid);

        $id = $activity->id;
        $typeOfActivity = $activity->name;

        switch($typeOfActivity){
            
            case 'assignment':
                return $this->getAssignment($id);
                break;

            case 'forum':
                return $this->getForum($id);
                break;

            case 'label':
                return $this->getLabel($id);
                break;

            case 'page':
                return $this->getPage($id);
                break;

            case 'quiz':
                return $this->getQuiz($id);
                break;

            case 'resource':
                return $this->getResource($id);
                break;

            case 'url':
                return $this->getUrl($id);
                break;

        }
    }

    private function getIdAndTypeOfActivity($coursemoduleid){
        $url = $this->getConfig()['MOODLE_API_URL'].'&coursemoduleid=%s';
        $url = sprintf($url, $this->getToken(), "get_activity_id_and_name", $coursemoduleid);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
        
        return new JsonModel((array)$json[0]);
    }

    private function getAssignment(){}

    private function getForum(){}

    private function getLabel(){}

    private function getPage(){}

    private function getQuiz($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&quizid=%s';
        $url = sprintf($url, $this->getToken(), "get_quiz", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
        
        $quiz = new MoodleQuiz($json[0]);
        
        return new JsonModel((array)$quiz);
    }

    private function getResource(){}

    private function getUrl(){}


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
        $url = sprintf($url, 'incluso', 'incluso', $this->getConfig()['MOODLE_SERVICE_NAME']);
        //$url = sprintf($url, 'test', 'Test123!', $this->getConfig()['MOODLE_SERVICE_NAME']);
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





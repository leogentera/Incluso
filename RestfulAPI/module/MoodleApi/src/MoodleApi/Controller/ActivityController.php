<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

use MoodleApi\Model\MoodleAssignment;
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

        if(array_key_exists("error", $activity)){
            return new JsonModel($this->throwJSONError("Actividad inválida, Contacte al administrador"));
        }

        $activityid = $activity->id;
        $typeOfActivity = $activity->name;

        switch($typeOfActivity){
            
            case 'assign':
                return $this->getAssignment($activityid);
                break;

            case 'forum':
                return $this->getForum($activityid);
                break;

            case 'label':
                return $this->getLabel($activityid);
                break;

            case 'page':
                return $this->getPage($activityid);
                break;

            case 'quiz':
                return $this->getQuiz($activityid);
                break;

            case 'url':
                return $this->getUrl($activityid);
                break;

            default:
                return new JsonModel($this->throwJSONError("Actividad no soportada, Contacte al administrador"));
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

        if(count($json) == 0){
            return array("error" => "error");
        }
        
        return new JsonModel((array)$json[0]);
    }

    private function getAssignment($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&assignmentid=%s';
        $url = sprintf($url, $this->getToken(), "get_assignment", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
       
        $assignment = new MoodleAssignment($json[0]);
        
        return new JsonModel((array)$assignment);
    }

    private function getForum($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&forumid=%s';
        $url = sprintf($url, $this->getToken(), "mod_forum_get_forum_discussions_paginated", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }

        $forum = new MoodleForum();
        $forum->setId($id);
        $summary = $this->getActivitySummary($id, 'forum');
        $forum->setName($summary->name);
        $forum->setDescription($summary->intro);
        $forum->setDiscussions($json["discussions"]);
        
        return new JsonModel((array)$forum);
    }

    private function getLabel($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&labelid=%s';
        $url = sprintf($url, $this->getToken(), "get_label", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
       
        $label = new MoodleLabel($json[0]);
        
        return new JsonModel((array)$label);
    }

    private function getPage($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&pageid=%s';
        $url = sprintf($url, $this->getToken(), "get_page", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
       
        $page = new MoodlePage($json[0]);
        
        return new JsonModel((array)$page);
    }

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

    private function getUrl($id){
        $url = $this->getConfig()['MOODLE_API_URL'].'&urlid=%s';
        $url = sprintf($url, $this->getToken(), "get_url", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array();
        }
       
        $url = new MoodleUrl($json[0]);
        
        return new JsonModel((array)$url);
    }

    private function getActivitySummary($instanceid, $typeOfActivity){
        $url = $this->getConfig()['MOODLE_API_URL'].'&instanceid=%s&typeOfActivity=%s';
        $url = sprintf($url, $this->getToken(), "get_activity_summary", $instanceid, $typeOfActivity);

        $response = file_get_contents($url);

        $json = json_decode($response,true);
        if (strpos($response, "exception") !== false){
            var_dump($response);
            return array();
        }

        return new JsonModel($json[0]);   
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





<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleActivity;
use MoodleApi\Model\MoodleChallenge;
use MoodleApi\Model\MoodleStage;
use MoodleApi\Model\MoodleUserCourse;

class UserCourseController extends AbstractRestfulJsonController {
    
    private $token = "";

    public function get($userid){

        $userCourse = new MoodleUserCourse($userid);

        //Get course
        $courseid = 2;

        $stages = $this->getCourseStages($courseid);

        $userCourse->setStages($stages);

        for($i = 0; $i < sizeof($stages); $i++){
            $userCourse->stages[$i]->setChallenges($this->getChallengesStage($userid, $courseid, $userCourse->stages[$i]->section));
            $userCourse->stages[$i]->setStageProgress($this->getProgressStage($userCourse->stages[$i]->id, $userid));

            $stageStatus = 0;

            if($userCourse->stages[$i]->stageProgress == 100){
                $stageStatus = 1;                
            }

            $userCourse->stages[$i]->setStageStatus($stageStatus);


        }

        return new JsonModel((array)$userCourse);

        //Get activities
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

    private function getCourseStages($courseid){
        $url = $this->getConfig()['MOODLE_API_URL'].'&courseid=%s';
        $url = sprintf($url, $this->getToken(), "get_stages_by_course", $courseid);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false)
        {
    
            return array();
        }
        // Good
        $stages= array();

        foreach($json as $stage){
            $stage = new MoodleStage($stage);
            array_push($stages, $stage);
        }
        return $stages;
    }


    private function getChallengesStage($userid, $courseid, $stageid){
    
        $url = $this->getConfig()['MOODLE_API_URL'].'&courseid=%s&stageid=%s';
        $url = sprintf($url, $this->getToken(), "get_challenges_stage", $courseid, $stageid);

        $response = file_get_contents($url);

        $json = json_decode($response,true);

        if (strpos($response, "exception") !== false){
            return array();
        }

        $challenges= array();

        foreach($json as $challenge){
            $challenge = new MoodleChallenge($challenge);
            $challenge->setActivityType("ActivityManager");
            $challenge->setActivities($this->getActivitiesByChallenge($userid, $courseid, $challenge->id));
            $challenge->setStatus(1);

            foreach ($challenge->activities as $activity) {
                if($activity->status == 0){
                    $challenge->setStatus(0);                    
                }
            }

            array_push($challenges, $challenge);
        }

        return $challenges;
    }

    private function getActivitiesByChallenge($userid, $courseid, $sectionid){
        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&mainActivity=%s&courseid=%s';
        $url = sprintf($url, $this->getToken(), "get_activities_status_by_challenge", $userid, $sectionid, $courseid);

        $response = file_get_contents($url);

        $json = json_decode($response,true);

        if (strpos($response, "exception") !== false){
            return array();
        }

        $activities = array();

        foreach($json as $act){
            $activity = new MoodleActivity($act);
            $activity->setStatus($act["completionstate"]);
            $activity->setTimeModified($act["timemodified"]);
            array_push($activities, $activity);
        }

        return $activities;
    }


    private function getProgressStage($stageid, $userid){

        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&stageid=%s';
        $url = sprintf($url, $this->getToken(), "get_stage_progress", $userid, $stageid);

        $response = file_get_contents($url);

        $json = json_decode($response,true);

        if (strpos($response, "exception") !== false){
            return array();
        }
            // Good
        if (count($json)==0){
            return "-1";
        }
        
        $progress=$json[0]['percentage_completed'];
        return $progress;
    }

}





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

        $userCourse = new MoodleUserCourse();

        $userCourse->setUser($userid);

        $courseid = $this->getCurrentCourse($userid);

        $userCourse->setCourse($courseid);

        $userCourse->setFirstTime($this->getIfIsFirstTime($courseid, $userid));

        $userCourse->setGlobalProgress($this->getGlobalProgress($userid, $courseid));

        $stages = $this->getCourseStages($courseid, $userid);

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
    }

    public function update($userid, $data){

        //Update Flag First Time
        if(array_key_exists("firstTime", $data)){
            //Update course firsttime
            $this->setFirstTime($userid, $data["courseId"], "course");
        }

        //Update Flag First Time
        if(array_key_exists("stages", $data)){;
            foreach ($data["stages"] as $stage) {
                if(array_key_exists("firstTime", $stage)){
                    //Update stage firstime;
                    $this->setFirstTime($userid, $stage["section"], "stage");
                }
            }
        }

        return new JsonModel(array("update"=>true));
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

    private function getCurrentCourse($userid){
        $url = $this->getConfig()['MOODLE_API_URL'].'&field=id&values[0]=%s';
        $url = sprintf($url, $this->getToken(), "core_user_get_users_by_field", $userid);
        
        $response = file_get_contents($url);

        $json = json_decode($response, true);
        
        if (strpos($response, "exception") !== false)
        {
            return new JsonModel( $this->throwJSONError("El usuario no esta registrado"));
        }
        else
        {
            for($i=0;count($json[0]['customfields'])>$i;$i++){
                $customFields[$json[0]['customfields'][$i]['name']]=$json[0]['customfields'][$i]['value'];
            }

            return $customFields["course"];
      
        }
    }

    private function getIfIsFirstTime($courseid, $userid){
        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&courseid=%s';
        $url = sprintf($url, $this->getToken(), "is_first_time_in_course", $userid, $courseid);
        
        $response = file_get_contents($url);

        $json = json_decode($response, true);
        
        if (strpos($response, "exception") !== false){
            return 1;
        }else{
            return $json[0]["firsttime"];
        }
    }

    private function getCourseStages($courseid, $userid){
        $url = $this->getConfig()['MOODLE_API_URL'].'&courseid=%s&userid=%s';
        $url = sprintf($url, $this->getToken(), "get_stages_by_user_and_course", $courseid, $userid);

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

    private function getGlobalProgress($userid, $courseid){
    
        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&courseid=%s';
        $url = sprintf($url, $this->getToken(), "get_global_progress", $userid, $courseid);

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

    private function setFirstTime($userid, $resource, $typeOfResource){
        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s&resourceid=%s&typeofresource=%s';
        $url = sprintf($url, $this->getToken(), "update_first_time_in_resource", $userid, $resource, $typeOfResource);
        
        $response = file_get_contents($url);

        $json = json_decode($response, true);
        
        if (strpos($response, "exception") !== false){
            return -1;
        }else{
            return $json[0]["id"];
        }
    }

}





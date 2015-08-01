<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;
use MoodleApi\Model\MoodleActivity;
use MoodleApi\Model\MoodleCourse;
use MoodleApi\Model\MoodleException;
use MoodleApi\Model\MoodleLeader;
use MoodleApi\Model\MoodleStage;
use MoodleApi\Model\MoodleChallenge;

class CourseController extends AbstractRestfulJsonController {
    
    private $token = "";
    private $function = "core_course_get_contents";

    public function get($id)
    {
        $course = new MoodleCourse();
        $leaders = $this->getLeaderboard($id);
        $stages = $this->getCourseStages($id);
        
        $course->setLeaderboard($leaders);

        $course->setStages($stages);

        for($i = 0; $i < sizeof($stages); $i++){
            $course->stages[$i]->setChallenges($this->getChallengesStage($id, $course->stages[$i]->section));
        }

        return new JsonModel((array)$course);
    }

    private function getLeaderboard($course){
    
        $url = $this->getConfig()['MOODLE_API_URL'].'&amount=3&courseid=%s';
        $url = sprintf($url, $this->getToken(), "get_leaderboard", $course);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false)
        {
   
            return array();
        }
        // Good
        $leaders= array();

        foreach($json as $leader){
            $leader = new MoodleLeader($leader);
            array_push($leaders, $leader);
        }
        return $leaders;
    }

    private function getChallengesStage($courseid, $stageid){
    
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
            $challenge->setActivities($this->getActivitiesByChallenge($challenge->id));
            array_push($challenges, $challenge);
        }

        return $challenges;
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

    private function getActivitiesByChallenge($sectionid){
        $url = $this->getConfig()['MOODLE_API_URL'].'&sectionid=%s';
        $url = sprintf($url, $this->getToken(), "get_activities_by_challenge", $sectionid);

        $response = file_get_contents($url);

        $json = json_decode($response,true);

        if (strpos($response, "exception") !== false){
            return array();
        }

        $activities= array();
        
        foreach($json as $activity){
            $activity = new MoodleActivity($activity);
            $activitysummary = $this->getActivitySummary($activity->id, $activity->activityType);
            $activity->setName($activitysummary->name);
            $activity->setDescription($activitysummary->intro);
            array_push($activities, $activity);
        }

        return $activities;
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
}





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

use Zend\Cache\StorageFactory;

class CourseController extends AbstractRestfulJsonController {
    
    private $token = "";
    private $function = "core_course_get_contents";

    public function get($id){

        $cache = StorageFactory::factory(array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'ttl' => 86400,
                    'cache_dir' => __DIR__."\..\Cache"),
            ),
            'plugins' => array(
                // Don't throw exceptions on cache errors
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            )
        ));

        // see if a cache already exists:
        $course = $cache->getItem('course', $success);

        if (!$success) {
         
            // cache miss
            
            $course = $this->getCourseStages($id);
            
            //$cache->setItem('course', $course);

        }else{

            $course = json_decode($course);

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
        $url = sprintf($url, $this->getToken(), "get_challenges_stage", $courseid);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false)
        {
    
            return array();
        }

        return $json;
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





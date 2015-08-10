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

        $courseAndStatus = $this->getCurrentCourseAndStatus($userid);

        $courseid = $courseAndStatus["courseid"];

        $userCourse->setCourse($courseid);
        $userCourse->setFirstTime($courseAndStatus["firsttime"]);
        $userCourse->setGlobalProgress($courseAndStatus["percentage_completed"]);

        $stages = $this->getCourseUserDetail($userid);

        $userCourse->setStages($stages);

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

    private function getCurrentCourseAndStatus($userid){

        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s';
        $url = sprintf($url, $this->getToken(), "get_current_course_and_status_by_user", $userid);
        
        $response = file_get_contents($url);

        $json = json_decode($response, true);

        if (strpos($response, "exception") !== false){
            return 1;
        }else{
            if(count($json)==0){
                return 1;
            }
            return $json;
        } 
    }

    private function getCourseUserDetail($userid){

        $url = $this->getConfig()['MOODLE_API_URL'].'&userid=%s';
        $url = sprintf($url, $this->getToken(), "get_user_course_info", $userid);
        
        $response = file_get_contents($url);

        $json = json_decode($response, true);

        if (strpos($response, "exception") !== false){
            return 1;
        }else{

            $lastStageId = -1;
            $lastChallengeId = -1;
            $stage = null;
            $challenge = null;
            $activity = null;
            $result = array();

            $totalActivityStage = 0;
            $totalActivityStageCompleted = 0;

            $totalActivityChallenge = 0;
            $totalActivityChallengeCompleted = 0;

            foreach($json as $row){
                $activity = new MoodleActivity($row);
                $activity->setId($row["activityid"]);
                $activity->setActivityType($row["activitytype"]);
                $activity->setCourseModuleId($row["coursemoduleid"]);
                $activity->setStatus($row["completionstate"]);
                $activity->setTimeModified($row["timemodified"]);

                
                if($row["stageid"] == $lastStageId){

                    $totalActivityStage++;
                    if($activity->status == 1){
                        $totalActivityStageCompleted++;
                    }

                    if($row["challengeid"] == $lastChallengeId){
                        
                        $totalActivityChallenge++;
                        if($activity->status == 1){
                            $totalActivityChallengeCompleted++;
                        }

                        $challenge->setActivities($activity);

                    }else{

                        if($totalActivityChallenge == $totalActivityChallengeCompleted){
                            $challengeStatus = 1;
                        }else{
                            $challengeStatus = 0;
                        }

                        $challenge->setStatus($challengeStatus);
                        $stage->setChallenges($challenge);
                        $lastChallengeId = $row["challengeid"];

                        $totalActivityChallenge = 1;
                        if($activity->status == 1){
                            $totalActivityChallengeCompleted++;
                        }

                        $challenge = new MoodleChallenge($row);
                        $challenge->setId($row["challengeid"]);
                        $challenge->setName($row["challenge"]);
                        $challenge->setDescription($row["challenge_description"]);
                        $challenge->setActivityType("ActivityManager");
                        $challenge->setActivities($activity);

                    } 
                }else{
                    if($lastStageId != -1){
                        
                        if($totalActivityStage != 0 && $totalActivityStageCompleted != 0){
                            $progress = (int) ($totalActivityStageCompleted*100/$totalActivityStage);
                        }else{
                            $progress = 0;
                        }

                        if($progress == 100){
                            $stageStatus = 1;
                        }else{
                            $stageStatus = 0;
                        }

                        if($totalActivityChallenge == $totalActivityChallengeCompleted){
                            $challengeStatus = 1;
                        }else{
                            $challengeStatus = 0;
                        }

                        $totalActivityStage = 0;
                        $totalActivityStageCompleted = 0;
                        $totalActivityChallenge = 0;
                        $totalActivityChallengeCompleted = 0;

                        $challenge->setStatus($challengeStatus);               
                        $stage->setStageProgress($progress);
                        $stage->setStageStatus($stageStatus);
                        $stage->setChallenges($challenge);
                        array_push($result, $stage);
                    }

                    $lastStageId = $row["stageid"];
                    $lastChallengeId = $row["challengeid"];

                    $totalActivityStage++;
                    $totalActivityChallenge++;

                    if($activity->status == 1){
                        $totalActivityStageCompleted++;
                        $totalActivityChallengeCompleted++;
                    }

                    $stage = new MoodleStage($row);
                    $stage->setId($row["stageid"]);
                    $stage->setName($row["stage"]);
                    $stage->setSection($row["stagesection"]);
                    $stage->setFirstTime($row["firsttime"]);

                    $challenge = new MoodleChallenge($row);
                    $challenge->setId($row["challengeid"]);
                    $challenge->setName($row["challenge"]);
                    $challenge->setDescription($row["challenge_description"]);
                    $challenge->setActivityType("ActivityManager");
                    $challenge->setActivities($activity);

                }
            }

            if($totalActivityStage !=0  && $totalActivityStageCompleted != 0){
                $progress = (int) ($totalActivityStageCompleted*100/$totalActivityStage);
            }else{
                $progress = 0;
            }

            if($progress == 100){
                $stageStatus = 1;
            }else{
                $stageStatus = 0;
            }

            if($totalActivityChallenge == $totalActivityChallengeCompleted){
                $challengeStatus = 1;
            }else{
                $challengeStatus = 0;
            }

            $challenge->setStatus($challengeStatus);

            $stage->setStageProgress($progress);
            $stage->setStageStatus($stageStatus);
            $stage->setChallenges($challenge);

            array_push($result, $stage);

            array_shift($result);

            return $result;
        } 
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
?>
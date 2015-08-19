<?php
namespace MoodleApi\Controller;

use MoodleApi\Utilities\AbstractRestfulJsonController;
use Zend\View\Model\JsonModel;

use MoodleApi\Model\MoodleAssignment;
use MoodleApi\Model\MoodleForum;
use MoodleApi\Model\MoodleForumDiscussion;
use MoodleApi\Model\MoodleForumPost;
use MoodleApi\Model\MoodleLabel;
use MoodleApi\Model\MoodlePage;
use MoodleApi\Model\MoodleQuiz;
use MoodleApi\Model\MoodleResource;
use MoodleApi\Model\MoodleUrl;

class ActivityController extends AbstractRestfulJsonController {

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

    public function getList(){

        if(array_key_exists("courseid", $_GET)){
            return new JsonModel($this->throwJSONError("Curso inválido, Contacte al administrador"));
        }

        $courseid = $_GET["course"];

        $url = $this->getConfig()['MOODLE_API_URL'].'&courseid=%s';
        $url = sprintf($url, $this->getToken(), "get_all_activities_by_course", $courseid);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return new JsonModel();
        }

        if(count($json) == 0){
            return new JsonModel();
        }
        
        return new JsonModel((array)$json);
    }

    private function getIdAndTypeOfActivity($coursemoduleid){
        $url = $this->getConfig()['MOODLE_API_URL'].'&coursemoduleid=%s';
        $url = sprintf($url, $this->getToken(), "get_activity_id_and_name", $coursemoduleid);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);
    
        if (strpos($response, "exception") !== false){
            return array("error" => "error");
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
        $forum = new MoodleForum();
        $forum->setId($id);
        $summary = $this->getActivitySummary($id, 'forum');
        $forum->setName($summary->name);
        $forum->setActivityType();
        $forum->setDescription($summary->intro);

        $url = $this->getConfig()['MOODLE_API_URL'].'&forumid=%s';
        $url = sprintf($url, $this->getToken(), "mod_forum_get_forum_discussions_paginated", $id);

        $response = file_get_contents($url);
    
        $json = json_decode($response,true);

        if (strpos($response, "exception") !== false){
            return new JsonModel((array)$forum);
        }

        $discussions = $json["discussions"];

        foreach($discussions as $discussion){
            $discussionObj = new MoodleForumDiscussion($discussion);

            $url = $this->getConfig()['MOODLE_API_URL'].'&discussionid=%s&sortdirection=ASC';
            $url = sprintf($url, $this->getToken(), "get_forum_discussion_posts", $discussion["discussion"]);

            $response = file_get_contents($url);
        
            $json = json_decode($response,true);

            if (strpos($response, "exception") == false){

                $posts = $json["posts"];

                $posts = $this->getTreeDiscussion($posts);

                $discussionObj->setPosts($posts);
            }

            $forum->setDiscussions($discussionObj);
        }

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

    private function getTreeDiscussion($posts){
        
        $new = array();

        foreach ($posts as $a){
            $new[$a['parent']][] = new MoodleForumPost($a);
        }

        $tree = $this->createTree($new, $new[0]); // changed

        return $tree;
    }

    private function createTree(&$list, $parent){
        $tree = array();

        foreach ($parent as $k=>$l){
            if(isset($list[$l->id])){
                $l->replies = $this->createTree($list, $list[$l->id]);
            }

            $tree[] = $l;
        } 
        
        return $tree;
    }
}





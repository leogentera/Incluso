<?php
namespace MoodleApi\Model;

class MoodleUserCourse
{
	public $courseId;
    public $userId;
    public $firstTime;
    public $stages=array();
    
    public function setCourse($courseid){
        $this->courseId = $courseid;
    }

    public function setFirstTime($time){
    	$this->firstTime = $time;
    }

    public function setStages($stage){
    	$this->stages = $stage;
    }

    public function setUser($userid){
        $this->userId = $userid;
    }
}
<?php
namespace MoodleApi\Model;

class MoodleUserCourse
{
	public $courseId;
    public $userId;
    public $firstTime;
    public $stages=array();
    
    public function __construct($data)
    {
        $this->courseId           = (!empty($data['instance'])) ? $data['instance'] : null;
        $this->userId = (!empty($data['name'])) ? $data['name'] : null;
    
    }

    public function setFirstTime($time){
    	$this->firstTime = $time;
    }

    public function setStages($stage){
    	$this->stages = $stage;
    }
}
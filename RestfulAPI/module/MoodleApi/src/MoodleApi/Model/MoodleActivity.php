<?php
namespace MoodleApi\Model;

class MoodleActivity
{
	public $id;
    public $name;
    public $description;
    public $activityType;
    public $coursemoduleid;

    //Using only in UserCourse
    public $status;
    public $timemodified;
    
    public function __construct($data)
    {
        $this->id             = (!empty($data['instance'])) ? $data['instance'] : null;
        $this->activityType   = (!empty($data['name'])) ? $data['name'] : null;
        $this->coursemoduleid = (!empty($data['coursemoduleid'])) ? $data['coursemoduleid'] : null;
    
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setDescription($description){
    	$this->description = $description;
    }

    public function setActivityType($activityType){
        $this->activityType = $activityType;
    }

    public function setCourseModuleId($courseModuleId){
        $this->coursemoduleid = $courseModuleId;
    }

    public function setStatus($status){
    	$this->status = $status;
    }

    public function setTimeModified($time){
        $this->timemodified = $time;
    }
}
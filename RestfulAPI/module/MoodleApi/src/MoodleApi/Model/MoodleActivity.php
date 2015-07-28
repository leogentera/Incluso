<?php
namespace MoodleApi\Model;

class MoodleActivity
{
	public $id;
    public $name;
    public $description;
    public $activityType;

    //Using only in UserCourse
    public $status;
    public $timemodified;
    
    public function __construct($data)
    {
        $this->id           = (!empty($data['instance'])) ? $data['instance'] : null;
        $this->activityType = (!empty($data['name'])) ? $data['name'] : null;
    
    }

    public function setDescription($description){
    	$this->description = $description;
    }

    public function setName($name){
    	$this->name = $name;
    }

    public function setStatus($status){
    	$this->status = $status;
    }

    public function setTimeModified($time){
        $this->timemodified = $time;
    }
}
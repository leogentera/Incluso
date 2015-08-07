<?php
namespace MoodleApi\Model;

class MoodleChallenge
{
	public $id;
    public $name;
    public $description;
    public $image;
    public $activityType;
    public $activities = array();
    
    //Using only in UserCourse
    public $status;

    public function __construct($data)
    {
        $this->id          = (!empty($data['sectionid'])) ? $data['sectionid'] : null;
        $this->name        = (!empty($data['name'])) ? $data['name'] : null;
        $this->description = (!empty($data['summary'])) ? $data['summary'] : null;        
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

    public function setActivityType($type){
        $this->activityType = $type;
    }

    public function setActivities($activities){
        array_push($this->activities, $activities);
    }

    public function setStatus($status){
        $this->status = $status;
    }
}
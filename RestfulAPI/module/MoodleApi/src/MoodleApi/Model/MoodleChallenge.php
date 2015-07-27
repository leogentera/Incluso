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
    
    public function __construct($data)
    {
        $this->id          = (!empty($data['sectionid'])) ? $data['sectionid'] : null;
        $this->name        = (!empty($data['name'])) ? $data['name'] : null;
        $this->description = (!empty($data['summary'])) ? $data['summary'] : null;        
    }

    public function setActivityType($type){
        $this->activityType = $type;
    }

    public function setActivities($activities){
    	$this->activities = $activities;
    }
}
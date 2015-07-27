<?php
namespace MoodleApi\Model;

class MoodleActivity
{
	public $id;
    public $name;
    public $description;
    public $activityType;
    
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
}
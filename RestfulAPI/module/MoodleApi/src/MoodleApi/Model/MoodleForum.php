<?php
namespace MoodleApi\Model;

class MoodleForum{

	public $id;
	public $name;
	public $description;
	public $activityType;
	public $stars;
	public $discussions = array();

    public function setId($id){
    	$this->id = $id;
    }

    public function setName($name){
    	$this->name = $name;
    }

    public function setDescription($description){
    	$this->description = $description;
    }

    public function setActivityType(){
        $this->activityType = 'forum';
    }

    public function setStars($stars){
    	$this->stars = $stars;
    }

    public function setDiscussions($discussions){
        array_push($this->discussions, $discussions);
    }
}
?>
<?php
namespace MoodleApi\Model;

class MoodleForum{

	public $id;
	public $name;
	public $description;
	public $activityType;
	public $stars;
	public $discussions = array();

	public function __construct($data){
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->name 		= (!empty($data['name'])) ? $data['name'] : null;
        $this->description  = (!empty($data['description'])) ? $data['description'] : null;
        $this->activityType = 'forum';
        $this->stars        = (!empty($data['stars'])) ? $data['stars'] : null;
        $this->discussions  = (!empty($data['discussions'])) ? $data['discussions'] : null;    
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

    public function setStars($stars){
    	$this->stars = $stars;
    }

    public function setDiscussions($discussions){
        $this->discussions = $discussions;
    }
}
?>
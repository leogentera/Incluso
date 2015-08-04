<?php
namespace MoodleApi\Model;

class MoodlePage{

	public $id;
	public $name;
	public $description;
	public $activityType;
	public $stars;
	public $pageContent;

	public function __construct($data){
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->name         = (!empty($data['name'])) ? $data['name'] : null;
        $this->description  = (!empty($data['description'])) ? $data['description'] : null;
        $this->activityType = 'page';
        $this->stars        = (!empty($data['stars'])) ? $data['stars'] : null;
        $this->pageContent  = (!empty($data['pagecontent'])) ? $data['pagecontent'] : null;    
    }

}
?>
<?php
namespace MoodleApi\Model;

class MoodleUrl{

	public $id;
	public $name;
	public $description;
	public $activityType;
	public $stars;
	public $url;

	public function __construct($data){
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->name         = (!empty($data['name'])) ? $data['name'] : null;
        $this->description  = (!empty($data['description'])) ? $data['description'] : null;
        $this->activityType = 'url';
        $this->stars        = (!empty($data['stars'])) ? $data['stars'] : null;
        $this->url          = (!empty($data['url'])) ? $data['url'] : null;
    }

}
?>
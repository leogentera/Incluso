<?php
namespace MoodleApi\Model;

class MoodleLabel{

	public $id;
	public $activityType;
	public $stars;
	public $labelText;

	public function __construct($data){
        $this->id           = (!empty($data['id'])) ? $data['id'] : null;
        $this->activityType = 'label';
        $this->stars        = (!empty($data['stars'])) ? $data['stars'] : null;
        $this->labelText    = (!empty($data['labeltext'])) ? $data['labeltext'] : null;    
    }

}
?>
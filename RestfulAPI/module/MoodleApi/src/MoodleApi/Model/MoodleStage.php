<?php
namespace MoodleApi\Model;

class MoodleStage
{
	public $id;
    public $stage;
    
    public function __construct($data)
    {
    	//var_dump($data);
        $this->id =     (!empty($data['stageid'])) ? $data['stageid'] : null;
        $this->stage =     (!empty($data['stage'])) ? $data['stage'] : null;
        
    }
}
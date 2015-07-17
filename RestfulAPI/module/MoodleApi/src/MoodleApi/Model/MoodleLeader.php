<?php
namespace MoodleApi\Model;

class MoodleLeader
{
	public $id;
    public $rank;
    public $fullname;
    public $progressPercentage=0;
    public $stars=-1;
    
    public function __construct($data)
    {
    	//var_dump($data);
        $this->id =     (!empty($data['id'])) ? $data['id'] : null;
        $this->fullname =     (!empty($data['name'])) ? $data['name'] : null;
        $this->rank =     (!empty($data['place'])) ? $data['place'] : null;
        $this->stars =     (!empty($data['stars'])) ? $data['stars'] : null;
        
    }
}
<?php
namespace MoodleApi\Model;

class MoodleLeader
{
	public $userId;
    public $rank;
    public $fullname;
    public $progress=0;
    public $stars=-1;
    
    public function __construct($data)
    {
    	//var_dump($data);
        $this->userId   = (!empty($data['id'])) ? $data['id'] : null;
        $this->fullname = (!empty($data['name'])) ? $data['name'] : null;
        $this->rank     = (!empty($data['place'])) ? $data['place'] : null;
        $this->stars    = (!empty($data['stars'])) ? $data['stars'] : 0;
        $this->progress = (!empty($data['percentage_completed'])) ? $data['percentage_completed'] : 0;
        
    }
}
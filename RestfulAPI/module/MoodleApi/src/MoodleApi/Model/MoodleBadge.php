<?php
namespace MoodleApi\Model;

class MoodleBadge
{
	public $id;
    public $name;
    public $description;
    public $earned_times;
    public $points;
    public $dateIssued;
    
    public function __construct($data)
    {
        $this->id =     (!empty($data['id'])) ? $data['id'] : null;
        $this->name =     (!empty($data['name'])) ? $data['name'] : null;
        $this->description =     (!empty($data['description'])) ? $data['description'] : null;
        $this->earned_times =     (!empty($data['earned_times'])) ? $data['earned_times'] : null;
        $this->points =     (!empty($data['points'])) ? $data['points'] : null;
        $mil=     (!empty($data['dateissued'])) ? $data['dateissued'] : null;
        
        if ($mil!=null)
        	$this->dateIssued= date("Y-m-d h:i:s", $mil);
        else
        	$this->dateIssued= null;
    }
}
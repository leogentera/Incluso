<?php
namespace MoodleApi\Model;

class MoodleSchool
{
	public $school;
    public $levelOfStudies;
    
    
    public function __construct($school, $levelOfStudies)
    {
    	$this->school=$school;
    	$this->levelOfStudies=$levelOfStudies;
    }
}
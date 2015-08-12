<?php
namespace MoodleApi\Model;

class MoodleCharacter
{
	public $characterName;
    public $characterType;
    
    
    public function __construct($characterName, $characterType)
    {
    	$this->characterName=$characterName;
    	$this->characterType=$characterType;
    }
}
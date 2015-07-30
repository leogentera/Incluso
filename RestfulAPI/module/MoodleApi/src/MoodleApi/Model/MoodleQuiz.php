<?php
namespace MoodleApi\Model;

class MoodleQuiz{
	
	public $id;
	public $name;
	public $description;
	public $activityType;
	public $status;
	public $stars;
	public $dateIssued;

	public $score;
	public $quizType;
	public $grade;
	public $questions = array();

	public function __construct($data){
		$this->id 			= (!empty($data['id'])) ? $data['id'] : null; 
		$this->name 		= (!empty($data['name'])) ? $data['name'] : null;
		$this->description 	= (!empty($data['description'])) ? $data['description'] : null;
		$this->activityType = (!empty($data['activityType'])) ? $data['activityType'] : null;
		$this->status 		= (!empty($data['status'])) ? $data['status'] : null;
		$this->stars 		= (!empty($data['stars'])) ? $data['stars'] : null;
		$this->dateIssued 	= (!empty($data['dateIssued'])) ? $data['dateIssued'] : null;
		$this->score 		= (!empty($data['score'])) ? $data['score'] : null;
		$this->quizType 	= (!empty($data['quizType'])) ? $data['quizType'] : null;
		$this->grade 		= (!empty($data['grade'])) ? $data['grade'] : null;
		$this->questions 	= (!empty($data['questions'])) ? $data['questions'] : null;
	}
}

?>
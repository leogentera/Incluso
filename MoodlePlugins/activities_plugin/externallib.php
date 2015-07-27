<?php
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/externallib.php");
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->libdir . "../../config.php"); 

class quiz_plugin extends external_api{
	
	public static function get_quiz_parameters(){
		return new external_function_parameters(
			array(
					'quizid' => new external_value(PARAM_INT, 'Quiz id ', VALUE_REQUIRED, null, false),
			)
		);
	}
	
	public static function get_quiz($quizid){
		global $USER;
		global $DB;
		$response = array();
		$sql="";
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_quiz_parameters(), 
					array(
						'quizid' => $quizid,
					));
		
			//Context validation
			//OPTIONAL but in most web service it should present
// 			$context = get_context_instance(CONTEXT_USER, $USER->id);
// 			self::validate_context($context);
		
			//Capability checking
			//OPTIONAL but in most web service it should present
			// if (!has_capability('moodle/user:viewdetails', $context)) {
			//     throw new moodle_exception('cannotviewprofile');
			// }
			
			
		
			$sql = "Select question.id id, question.questiontext question, question.qtype qtype
						from {quiz_slots} slot, {question} question
						where question.id=slot.questionid
						and slot.quizid=$quizid";
			
			
			$response = $DB->get_records_sql($sql);
			
			$questions_tmp=$response;
			
			$questions= array();
			foreach ($questions_tmp as $question){
				//$question= (array) $question;
				$questionid=$question->id;
				$sql = "Select answer, answers.id id
						from {question} question, {question_answers} answers
						where answers.question=question.id
						and answers.question=$questionid";
					
				
				$response = $DB->get_records_sql($sql);
				$question->answers=$response;
				array_push($questions, $question);
			}
			
			
			$sql = "Select quiz.id id, quiz.name name, intro description
					from {quiz} quiz where id=$quizid";
				
				
			$response = $DB->get_records_sql($sql);
			
			$quizes = array();
			foreach ($response as $quiz){
				$quiz->questions=$questions;
				$quiz->quiztype='';
				$quiz->stars=-1;
				array_push($quizes, $quiz);
			}
			
			
			$response=$quizes;
			
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_quiz_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'stars' => new external_value(PARAM_INT, 'Stars obtained'),
					'name' => new external_value(PARAM_TEXT, 'Name of the quiz'),
					'quiztype' => new external_value(PARAM_TEXT, 'quiz Type'),
					'description' => new external_value(PARAM_RAW, 'Quiz description'),
					'questions' =>  new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'question'  => new external_value(PARAM_RAW, 'Question'),
                                        'id' => new external_value(PARAM_INT, 'Question id'),
                                    	'qtype' => new external_value(PARAM_TEXT, 'Question Type'),
                                    	'answers' =>  new external_multiple_structure(
				                                new external_single_structure(
				                                    array(
				                                        'answer'  => new external_value(PARAM_RAW, 'Answer'),
				                                        'id' => new external_value(PARAM_INT, 'Answer id')
				                                    )
				                                ), 'Answer', VALUE_OPTIONAL),
                                    )
                                ), 'Quiz\'s questions', VALUE_OPTIONAL),
					'id' => new external_value(PARAM_INT, 'Percentage of progress of the user (completed/total activities)')
				)
			)
		);
	}
	
	
	public static function get_quiz_result_parameters(){
		return new external_function_parameters(
				array(
						'quizid' => new external_value(PARAM_INT, 'Quiz id ', VALUE_REQUIRED, null, false),
						'userid' => new external_value(PARAM_INT, 'User id ', VALUE_REQUIRED, null, false),
				)
		);
	}
	
	public static function get_quiz_result($quizid, $userid){
		global $USER;
		global $DB;
		$response = array();
		$sql="";
	
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_quiz_result_parameters(),
					array(
							'quizid' => $quizid,
							'userid' => $userid,
					));
	
			//Context validation
			//OPTIONAL but in most web service it should present
			// 			$context = get_context_instance(CONTEXT_USER, $USER->id);
			// 			self::validate_context($context);
	
			//Capability checking
			//OPTIONAL but in most web service it should present
			// if (!has_capability('moodle/user:viewdetails', $context)) {
			//     throw new moodle_exception('cannotviewprofile');
			// }
			
			$sql = "Select question.id id, question.questiontext question, question.qtype qtype
			from {quiz_slots} slot, {question} question
			where question.id=slot.questionid
			and slot.quizid=$quizid";
				
				
			$response = $DB->get_records_sql($sql);
				
			$questions_tmp=$response;
				
			$questions= array();
			foreach ($questions_tmp as $question){
				//$question= (array) $question;
				$questionid=$question->id;
				$sql = "Select answer, answers.id id
				from {question} question, {question_answers} answers
				where answers.question=question.id
				and answers.question=$questionid";
					
			
				$response = $DB->get_records_sql($sql);
				$question->answers=$response;
				
				$sql = "Select  quiza.userid, qa.responsesummary, qa.questionid, quiza.quiz, qa.rightanswer rightanswer
						FROM {quiz_attempts} quiza
						JOIN {question_usages} qu ON qu.id = quiza.uniqueid
						JOIN {question_attempts} qa ON qa.questionusageid = qu.id,
						(Select max(qa.id) id
						FROM {quiz_attempts} quiza
						JOIN {question_usages} qu ON qu.id = quiza.uniqueid
						JOIN {question_attempts} qa ON qa.questionusageid = qu.id
						where quiza.quiz=$quizid and quiza.userid=$userid
						and not isnull(qa.responsesummary)
						group by  questionid, userid) lastAttempt
						where quiza.quiz=$quizid and quiza.userid=$userid
						and qa.questionid=$questionid
						and lastAttempt.id=qa.id";
					
					
				$response = $DB->get_records_sql($sql);
				
				$answered=reset($response);
				$question->rightAnswer=$answered->rightanswer;
				$question->selectedAnswer=$answered->responsesummary;
				array_push($questions, $question);
			}
				
				
			$sql = "Select quiz.id id, quiz.name name, intro description
			from {quiz} quiz where id=$quizid";
			
			
			$response = $DB->get_records_sql($sql);
				
			$quizes = array();
			foreach ($response as $quiz){
				$quiz->questions=$questions;
				$quiz->quiztype='';
				$quiz->stars=-1;
				$quiz->score=-1;
				array_push($quizes, $quiz);
			}
				
				
			$response=$quizes;
				
				
	
				
	
		} catch (Exception $e) {
			var_dump($e);
			$response = $e;
		}
		return $response;
	}
	
	public static function get_quiz_result_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'stars' => new external_value(PARAM_INT, 'Stars obtained'),
					'score' => new external_value(PARAM_INT, 'Score obtained'),
					'name' => new external_value(PARAM_TEXT, 'Name of the quiz'),
					'quiztype' => new external_value(PARAM_TEXT, 'quiz Type'),
					'description' => new external_value(PARAM_RAW, 'Quiz description'),
					'questions' =>  new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'question'  => new external_value(PARAM_RAW, 'Question'),
                                        'id' => new external_value(PARAM_INT, 'Question id'),
                                    	'qtype' => new external_value(PARAM_TEXT, 'Question Type'),
                                    	'rightAnswer' => new external_value(PARAM_RAW, 'Question Type'),
                                    	'selectedAnswer' => new external_value(PARAM_RAW, 'Question Type'),
                                    	'answers' =>  new external_multiple_structure(
				                                new external_single_structure(
				                                    array(
				                                        'answer'  => new external_value(PARAM_RAW, 'Answer'),
				                                        'id' => new external_value(PARAM_INT, 'Answer id')
				                                    )
				                                ), 'Answer', VALUE_OPTIONAL),
                                    )
                                ), 'Quiz\'s questions', VALUE_OPTIONAL),
					'id' => new external_value(PARAM_INT, 'Percentage of progress of the user (completed/total activities)')
				)
			)
		);
	}
	
	
	
}

class activitiesSummary_plugin extends external_api{

	public static function get_activity_summary_parameters(){
		return new external_function_parameters(
			array(
					'instanceid' => new external_value(PARAM_INT, 'Instance ID of activity'),
					'typeOfActivity'  => new external_value(PARAM_TEXT, 'Type of activity')
			)
		);
	}

	public static function get_activity_summary($instanceid, $typeOfActivity){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_activity_summary_parameters(), 
					array(
						'instanceid' => $instanceid,
						'typeOfActivity' => $typeOfActivity
					));
		
			//Context validation
			//OPTIONAL but in most web service it should present
			$context = get_context_instance(CONTEXT_USER, $USER->id);
			self::validate_context($context);
		
			//Capability checking
			//OPTIONAL but in most web service it should present
			// if (!has_capability('moodle/user:viewdetails', $context)) {
			//     throw new moodle_exception('cannotviewprofile');
			// }
		
			$sql = "SELECT id, name, intro FROM ";

			switch($typeOfActivity){
				case 'quiz':
					$sql.= "{quiz}";
					break;
				case 'forum':
					$sql.= "{forum}";
					break;
				case 'chat':
					$sql.= "{chat}";
					break;
				case 'resource':
					$sql.= "{resource}";
					break;
				case 'page':
					$sql.= "{page}";
					break;
				case 'url':
					$sql.= "{url}";
					break;
				case 'label':
					$sql.= "{label}";
					break;
				case 'assignment':
					$sql.= "{assignment}";
					break;
			}

			$sql.= " WHERE id=$instanceid";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}

	public static function get_activity_summary_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'id' => new external_value(PARAM_INT, 'Activity ID'),
					'name' => new external_value(PARAM_TEXT, 'Name of the activity'),
					'intro' => new external_value(PARAM_RAW, 'Introduction of activity')
				)
			)
		);
	}
}
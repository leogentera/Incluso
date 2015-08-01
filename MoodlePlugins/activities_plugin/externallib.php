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
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

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
			
			
		
			$sql = "Select question.id id, question.questiontext question, question.qtype questionType
						from {quiz_slots} slot, {question} question
						where question.id=slot.questionid
						and slot.quizid=$quizid";
			
			
			$response = $DB->get_records_sql($sql);
			
			$questions_tmp=$response;
			
			$questions= array();
			foreach ($questions_tmp as $question){
				//$question= (array) $question;
				$questionid=$question->id;
				$sql = "Select answer, answers.id id, answers.fraction fraction
						from {question} question, {question_answers} answers
						where answers.question=question.id
						and answers.question=$questionid";
					
				
				$response = $DB->get_records_sql($sql);
				
// 				foreach($response as $answer){
// 					$answer->answer=strip_tags($answer->answer);
// 				}
				
				$question->answers=$response;
				$question->userAnswer="";
				$question->questionType=$question->questiontype;
				array_push($questions, $question);
			}
			
			
// 			$sql = "Select quiz.id id, quiz.name name, intro description
// 					from {quiz} quiz where id=$quizid";

			$sql = "Select quiz.id id, quiz.name name, intro description, quiz.sumgrades sumgrades, quiz.grade grade
			from {quiz} quiz 
			where quiz.id=$quizid;";
				
				
			
			$response = $DB->get_records_sql($sql);
			
			$quizes = array();
			foreach ($response as $quiz){
				$quiz->questions=$questions;
				$quiz->quizType='';
				$quiz->stars=-1;
				$quiz->score=-1;
				$quiz->activityType="Quiz";
				
				$quiz->status=-1;
				$quiz->dateIssued="";
				array_push($quizes, $quiz);
			}
			
			
			$response=$quizes;
			
			//var_dump($response);
			
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_quiz_returns(){
		return new external_multiple_structure(
				new external_single_structure(
						array(
								'id' => new external_value(PARAM_INT, 'Quiz id'), //
								'name' => new external_value(PARAM_TEXT, 'Name of the quiz'), //
								'description' => new external_value(PARAM_RAW, 'Quiz description'), //
								'activityType' => new external_value(PARAM_TEXT, 'Type of activity'), //
								'status' => new external_value(PARAM_TEXT, 'For knowing if the activity was compled or not, Always returns -1'), //
								'stars' => new external_value(PARAM_INT, 'Stars to be obtained'),//
								'dateIssued' => new external_value(PARAM_RAW, 'Date time when the quiz was answered'),//
								'score' => new external_value(PARAM_RAW, 'User score, It is always empty due it is user field'),//
								'quizType' => new external_value(PARAM_TEXT, 'quiz Type'),//
								'questions' =>  new external_multiple_structure(
										new external_single_structure(
												array(
														'id' => new external_value(PARAM_INT, 'Question id'),//
														'question'  => new external_value(PARAM_RAW, 'Question'),//
														'questiontype' => new external_value(PARAM_TEXT, 'Question Type'),//
														'answers' =>  new external_multiple_structure(
																new external_single_structure(
																		array(
																				'id' => new external_value(PARAM_INT, 'Answer id'),//
																				'answer'  => new external_value(PARAM_RAW, 'Answer'),//
																				'fraction'  => new external_value(PARAM_RAW, 'fraction of points to be earned')//
																					
																		)
																), 'Possible Answers', VALUE_OPTIONAL),
														'userAnswer' => new external_value(PARAM_RAW, 'User Answer, Always empty due it is user field'),
															
												)
										), 'Quiz\'s questions', VALUE_OPTIONAL),
								'sumgrades' => new external_value(PARAM_NUMBER, 'Sumatory of all the question points on a quiz'),
								'grade' => new external_value(PARAM_NUMBER, 'Max grade to be earned'),
									
									
						)
				)
		);
// 		return new external_multiple_structure(
// 			new external_single_structure(
// 				array(
// 					'stars' => new external_value(PARAM_INT, 'Stars obtained'),
// 					'name' => new external_value(PARAM_TEXT, 'Name of the quiz'), -
// 					'quiztype' => new external_value(PARAM_TEXT, 'quiz Type'),
// 					'description' => new external_value(PARAM_RAW, 'Quiz description'),
// 					'questions' =>  new external_multiple_structure(
//                                 new external_single_structure(
//                                     array(
//                                         'question'  => new external_value(PARAM_RAW, 'Question'),
//                                         'id' => new external_value(PARAM_INT, 'Question id'),
//                                     	'qtype' => new external_value(PARAM_TEXT, 'Question Type'),
//                                     	'answers' =>  new external_multiple_structure(
// 				                                new external_single_structure(
// 				                                    array(
// 				                                        'answer'  => new external_value(PARAM_RAW, 'Answer'),
// 				                                        'id' => new external_value(PARAM_INT, 'Answer id')
// 				                                    )
// 				                                ), 'Answer', VALUE_OPTIONAL),
//                                     )
//                                 ), 'Quiz\'s questions', VALUE_OPTIONAL),
// 					'id' => new external_value(PARAM_INT, 'Percentage of progress of the user (completed/total activities)') -
// 				)
// 			)
// 		);
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
			
			$sql = "Select question.id id, question.questiontext question, question.qtype questionType
			from {quiz_slots} slot, {question} question
			where question.id=slot.questionid
			and slot.quizid=$quizid";
				
				
			$response = $DB->get_records_sql($sql);
				
			$questions_tmp=$response;
				
			$questions= array();
			foreach ($questions_tmp as $question){
				//$question= (array) $question;
				$questionid=$question->id;
				$sql = "Select answer, answers.id id , answers.fraction fraction
				from {question} question, {question_answers} answers
				where answers.question=question.id
				and answers.question=$questionid";
					
			
				$response = $DB->get_records_sql($sql);
				$question->answers=$response;
				
				$sql = "Select  quiza.userid, qa.responsesummary, qa.questionid, quiza.quiz
						FROM {quiz_attempts} quiza
						JOIN {question_usages} qu ON qu.id = quiza.uniqueid
						JOIN {question_attempts} qa ON qa.questionusageid = qu.id,
						(Select min(qa.id) id
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
				//$question->rightAnswer=$answered->rightanswer;
				$question->questionType=$question->questiontype;
				$question->userAnswer=$answered->responsesummary;
				array_push($questions, $question);
			}
				
				
// 			$sql = "Select quiz.id id, quiz.name name, intro description, quiz.sumgrades sumgrades, quiz.grade grade
// 			from {quiz} quiz where id=$quizid";
			
			
// 			$sql = "Select quiz.id id, quiz.name name, intro description, quiz.sumgrades sumgrades, quiz.grade grade, ifnull(completed.completed, 0) status, ifnull(completed.dateIssued, \"\") \"dateIssued\"
// 					from {quiz} quiz left join     
// 					(select mo.instance quizid,  IF(isnull(compl.timemodified), 0, 1) completed, FROM_UNIXTIME(timemodified) dateIssued
// 					from {course_modules} mo 
// 					join {course_modules_completion} compl 
// 					on  mo.id=compl.coursemoduleid 
// 					join {modules} module
// 					on module.id = mo.module
// 					where userid=$userid
// 					and module.name='quiz') completed on completed.quizid=quiz.id
// 					where quiz.id=$quizid;";
			
			$sql = "Select quiz.id id, quiz.name name, intro description, quiz.sumgrades sumgrades, quiz.grade grade, ifnull(completed.completed, 0) status, completed.dateIssued, truncate((quiza.sumgrades * quiz.grade)/quiz.sumgrades, 2) score
			from {quiz} quiz left join
			(select mo.instance quizid,  IF(isnull(compl.timemodified), 0, 1) completed, FROM_UNIXTIME(timemodified) dateIssued
			from {course_modules} mo
			join {course_modules_completion} compl
			on  mo.id=compl.coursemoduleid
			join {modules} module
			on module.id = mo.module
			where userid=$userid
			and module.name='quiz') completed on completed.quizid=quiz.id
			left join {quiz_attempts} quiza on quiz.id = quiza.quiz
			join (Select min(quiza.attempt) id
			FROM {quiz_attempts} quiza
			JOIN {question_usages} qu ON qu.id = quiza.uniqueid
			JOIN {question_attempts} qa ON qa.questionusageid = qu.id
			where quiza.quiz=$quizid and quiza.userid=$userid
			and not isnull(qa.responsesummary)) lastAttempt
			on lastAttempt.id=quiza.attempt
			where quiz.id=$quizid;";
			
			$response = $DB->get_records_sql($sql);
				
			$quizes = array();
			foreach ($response as $quiz){
				$quiz->questions=$questions;
				$quiz->quizType='';
				$quiz->stars=-1;
				//$quiz->score=-1;
				$quiz->activityType="Quiz";
				$quiz->dateIssued=$quiz->dateissued;
				array_push($quizes, $quiz);
			}
				
			//var_dump($sql);
				
			$response=$quizes;
				
				
	
				
	
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function get_quiz_result_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'id' => new external_value(PARAM_INT, 'Quiz id'),
					'name' => new external_value(PARAM_TEXT, 'Name of the quiz'),
					'description' => new external_value(PARAM_RAW, 'Quiz description'),
					'activityType' => new external_value(PARAM_TEXT, 'Type of activity'),
					'status' => new external_value(PARAM_TEXT, 'For knowing if the activity was compled or not, Returns 0 or 1 depending if the student finish the activity or not'),
					'stars' => new external_value(PARAM_RAW, 'Stars obtained'),
					'dateIssued' => new external_value(PARAM_RAW, 'Date time when the quiz was answered'),
					'score' => new external_value(PARAM_NUMBER, 'Score'),
					'quizType' => new external_value(PARAM_TEXT, 'quiz Type'),
					'questions' =>  new external_multiple_structure(
							new external_single_structure(
									array(
											'id' => new external_value(PARAM_INT, 'Question id'),
											'question'  => new external_value(PARAM_RAW, 'Question'),
											'questionType' => new external_value(PARAM_TEXT, 'Question Type'),
											'answers' =>  new external_multiple_structure(
													new external_single_structure(
															array(
																	'id' => new external_value(PARAM_INT, 'Answer id'),
																	'answer'  => new external_value(PARAM_RAW, 'Answer'),
																	'fraction'  => new external_value(PARAM_RAW, 'fraction of points to be earned')
											
															)
													), 'Possible Answers', VALUE_OPTIONAL),
											'userAnswer' => new external_value(PARAM_RAW, 'User Answer'),
											
										)
								), 'Quiz\'s questions', VALUE_OPTIONAL),
					'sumgrades' => new external_value(PARAM_NUMBER, 'Sumatory of all the question points on a quiz'),
					'grade' => new external_value(PARAM_NUMBER, 'Max grade to be earned'),
					
					
				)
			)
		);
	}
	
	
	//
	
	public static function save_quiz_parameters(){
		return new external_function_parameters(
				array(
						'quizid' => new external_value(PARAM_INT, 'Quiz id ', VALUE_REQUIRED, null, false),
						'userid' => new external_value(PARAM_INT, 'User id ', VALUE_REQUIRED, null, false),
						'answers' =>  new external_multiple_structure(
// 							new external_single_structure(
// 									array(
											//'answer'  => new external_value(PARAM_RAW, 'answer'),
											new external_value(PARAM_RAW, 'Answer')
											
// 										)
								/*)*/, 'Selected answers, Be sure to add the position index on multichoice or the inout text on the other cases', VALUE_REQUIRED, null, false)
				)
		);
	}
	
	public static function save_quiz($quizid, $userid, $answers){
		global $USER;
		global $DB;
		$sql="";
	
		try {
			//Parameter validation
			//REQUIRED
			
			$params = self::validate_parameters(
					self::save_quiz_parameters(),
					array(
							'quizid' => $quizid,
							'userid' => $userid,
							'answers' => $answers,
					));
	
			
				
			
	
	
			$result=self::saveQuizResult($quizid, $userid, $answers);
			$response=array();
			$response['message']=$result;
	
	
	
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}
	
	public static function save_quiz_returns(){
// 		return new external_multiple_structure(
// 				new external_single_structure(
// 						array(
// 								'message' => new external_value(PARAM_TEXT, 'Quiz id'),
// 						)
// 				)
// 		);

		return new external_function_parameters(
				array(
								'message' => new external_value(PARAM_TEXT, 'Message'),
						)
		);
	}
	
	
	/**
	 *
	 * @param int $cmid Course module id (Module id of the question)
	 * @param int  $userid Course id
	 * @param int  $answers Array with the answers in "Slot order", slot order is on the questions_attempts table, null for not aswered
	 */
	public static function saveQuizResult($quizid, $userid, $answers){
		$typeOfActivity='quiz';
		global $DB;
		//Let's first add the attempt
	
		if ($quizid==null) {
			http_response_code(404);
			return "Invalid quizid";
		}
	
		//$DB=$GLOBALS['DB'];
		$sql = "select module.id moduleid
		from {course_modules} module,
		{modules} module_desc
		where module.module=module_desc.id
		and module_desc.name='$typeOfActivity'
		and module.instance=$quizid";
	
		$cmid = key($DB->get_records_sql($sql));
	
	
		if (!$cm = get_coursemodule_from_id('quiz', $cmid)) {
			http_response_code(404);
			return "Invalid course module id";
		}
		if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
			http_response_code(404);
			return "Course is misconfigured";
		}
		$quizobj = quiz::create($cm->instance, $userid);
	
		if (!$quizobj->has_questions()) {
			http_response_code(404);
			return "Quiz doesn't have questions";
		}
	
		// Create an object to manage all the other (non-roles) access rules.
		$timenow = time();
		$accessmanager = $quizobj->get_access_manager($timenow);
	
		// Look for an existing attempt.
		$attempts = quiz_get_user_attempts($quizobj->get_quizid(), $userid, 'all', true);
		$lastattempt = end($attempts);
	
		// If an in-progress attempt exists, check password then redirect to it.
		if ($lastattempt && ($lastattempt->state == quiz_attempt::IN_PROGRESS ||
				$lastattempt->state == quiz_attempt::OVERDUE)) {
					$currentattemptid = $lastattempt->id;
					$messages = $accessmanager->prevent_access();
	
					// If the attempt is now overdue, deal with that.
					$quizobj->create_attempt_object($lastattempt)->handle_if_time_expired($timenow, true);
	
					// And, if the attempt is now no longer in progress, redirect to the appropriate place.
					if ($lastattempt->state == quiz_attempt::ABANDONED || $lastattempt->state == quiz_attempt::FINISHED) {
						//redirect($quizobj->review_url($lastattempt->id));
						http_response_code(404);
							
						return "Attempt no longer in progress";
					}
	
	
	
				} else {
					while ($lastattempt && $lastattempt->preview) {
						$lastattempt = array_pop($attempts);
					}
	
					// Get number for the next or unfinished attempt.
					if ($lastattempt) {
						$attemptnumber = $lastattempt->attempt + 1;
					} else {
						$lastattempt = false;
						$attemptnumber = 1;
					}
					$currentattemptid = null;
					$messages = $accessmanager->prevent_access() +
					$accessmanager->prevent_new_attempt(count($attempts), $lastattempt);
	
				}
					
				if ($accessmanager->is_preflight_check_required($currentattemptid)) {
					// Need to do some checks before allowing the user to continue.
					$mform = $accessmanager->get_preflight_check_form(
							$quizobj->start_attempt_url($page), $currentattemptid);
						
					if ($mform->is_cancelled()) {
						$accessmanager->back_to_view_page($output);
							
					} else if (!$mform->get_data()) {
							
						// Form not submitted successfully, re-display it and stop.
						http_response_code(404);
						return "Form not submitted successfully";
					}
						
					// Pre-flight check passed.
					$accessmanager->notify_preflight_check_passed($currentattemptid);
				}
					
					
				if (!$currentattemptid) {
					//If there is not pending intent, lets make a new one
	
					// Delete any previous preview attempts belonging to this user.
					quiz_delete_previews($quizobj->get_quiz(), $userid);
	
					$quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
					$quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
	
					// Create the new attempt and initialize the question sessions
					$timenow = time(); // Update time now, in case the server is running really slowly.
	
					$attempt = quiz_create_attempt($quizobj, $attemptnumber, $lastattempt, $timenow, false, $userid);
					//$attempt = quiz_create_attempt($quizobj, $currentattemptid, $lastattempt, $timenow, false, $userid);
					ob_start();
					if (!($quizobj->get_quiz()->attemptonlast && $lastattempt)) {
						$attempt = quiz_start_new_attempt($quizobj, $quba, $attempt, $attemptnumber, $timenow);
					} else {
						$attempt = quiz_start_attempt_built_on_last($quba, $attempt, $lastattempt);
					}
					ob_get_clean();
					$transaction = $DB->start_delegated_transaction();
	
					$attempt = quiz_attempt_save_started($quizobj, $quba, $attempt);
	
					$transaction->allow_commit();
	
					$sql = "SELECT id FROM {quiz_attempts} where quiz=$quizid and userid=$userid and attempt=$attemptnumber";
					$currentattemptid= key($DB->get_records_sql($sql));
	
	
				}
					
				if ($answers==null) {
					http_response_code(404);
					return "There is not an answers object";
				}
				else if (count($answers)==0){
					http_response_code(404);
					return "There is not an answer on the array";
				}
				
				//Sample of the original request
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="q41:1_:flagged"
				
// 				0
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="q41:1_:flagged"
				
// 				0
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="q41:1_:sequencecheck"
				
// 				1
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="q41:1_answer"
				
// 				0                                                          //Notice that this is not the id, is the position index
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="q41:2_:flagged"
				
// 				0
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="q41:2_:flagged"
				
// 				0
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="q41:2_:sequencecheck"
				
// 				1
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="q41:2_answer"
				
// 				0
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="next"
				
// 				Next
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="attempt"
				
// 				33
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="thispage"
				
// 				0
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="nextpage"
				
// 				-1
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="timeup"
				
// 				0
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="scrollpos"
				
				
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="slots"
				
// 				1,2
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="userid"
				
// 				3
// 				-----------------------------27812684618256
// 				Content-Disposition: form-data; name="finishattempt"
				
// 				true
// 				-----------------------------27812684618256--
				
				$timeup=0;
				$thispage=0;
				$next="Next";
				$nextpage=-1;
					
				// 			$sql = "SELECT uniqueid 'questionusageid', id attempt FROM {quiz_attempts} where quiz=$quizid and userid=$userid and attempt=$currentattemptid";
				$sql = "SELECT uniqueid 'questionusageid' FROM {quiz_attempts} where id=$currentattemptid";
				$usage= key($DB->get_records_sql($sql));
				$i=1;
				$slots="";
				


				foreach ($answers as $answer){
					$prefix="q$usage:".$i."_";
					$_POST[$prefix."answer"]=$answer;
					$_POST[$prefix.":flagged"]=0;
					$_POST[$prefix.":flagged"]=0;
					$_POST[$prefix.":sequencecheck"]=1;
					if ($slots!=""){
						$slots.=",";
					}
					$slots.=$i;
					$i++;
				}
					
				$_POST["userid"]=$userid;
				$_POST["slots"]=$slots;
				$_POST["timeup"]=$timeup;
				$_POST["thispage"]=$thispage;
				$_POST["next"]=$next;
				$_POST["nextpage"]=$nextpage;
				$transaction = $DB->start_delegated_transaction();
				$attemptobj = quiz_attempt::create($currentattemptid);
					
				// Remember the current time as the time any responses were submitted
				// (so as to make sure students don't get penalized for slow processing on this page).
				$timenow = time();
					
				$graceperiodmin = null;
				$accessmanager = $attemptobj->get_access_manager($timenow);
				$timeclose = $accessmanager->get_end_time($attemptobj->get_attempt());
					
				if ($attemptobj->get_userid() != $userid) {
					http_response_code(404);
					return "This is not your attempt";
				}
					
				if ($attemptobj->is_finished()) {
					http_response_code(404);
					return "Attempt is already closed";
				}
					
				try {
					// 				if ($becomingabandoned) {
					// 					$attemptobj->process_abandon($timenow, true);
					// 				} else {
	
						
					$attemptobj->process_finish($timenow, true);
						
					// 				}
						
				} catch (question_out_of_sequence_exception $e) {
					http_response_code(404);
					return "Submition out of sequence";
						
				} catch (Exception $e) {
					// This sucks, if we display our own custom error message, there is no way
					// to display the original stack trace.
					$debuginfo = '';
					if (!empty($e->debuginfo)) {
						$debuginfo = $e->debuginfo;
					}
					//     print_error('errorprocessingresponses', 'question',
					//             $attemptobj->attempt_url(null, $thispage), $e->getMessage(), $debuginfo);
					$http_response_code(404);
					return "Error processing";
				}
					
				// Send the user to the review page.
				$transaction->allow_commit();
				
				return "";
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
				case 'lesson':
					$sql.= "{lesson}";
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

	public static function get_activity_id_and_name_parameters(){
		return new external_function_parameters(
			array(
				'coursemoduleid' => new external_value(PARAM_INT, 'ID of table course_modules'),
			)
		);
	}

	public static function get_activity_id_and_name($coursemoduleid){
		global $USER;
		global $DB;
		$response = array();
		
		try {
			//Parameter validation
			//REQUIRED
			$params = self::validate_parameters(
					self::get_activity_id_and_name_parameters(), 
					array(
						'coursemoduleid' => $coursemoduleid
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
		
			$sql = "SELECT cm.instance AS id, m.name
					FROM mdl_course_modules AS cm
					INNER JOIN mdl_modules AS m
					ON cm.module = m.id
					WHERE cm.id = $coursemoduleid";
			
			$response = $DB->get_records_sql($sql);
		
		} catch (Exception $e) {
			$response = $e;
		}
		return $response;
	}

	public static function get_activity_id_and_name_returns(){
		return new external_multiple_structure(
			new external_single_structure(
				array(
					'id' => new external_value(PARAM_INT, 'Activity ID'),
					'name' => new external_value(PARAM_TEXT, 'Type of the activity')
				)
			)
		);
	}	
}
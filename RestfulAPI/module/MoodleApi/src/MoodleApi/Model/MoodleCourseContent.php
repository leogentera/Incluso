<?php
namespace MoodleApi\Model;

use MoodleApi\Model\MoodleModule;

class MoodleCourseContent
{
	public $id;
	public $name;
	public $visible;
	public $summary;
	public $module;
	
	
		
	public function exchangeArray($data)
	{
		$this->id     = (!empty($data['id'])) ? $data['id'] : null;
		$this->name     = (!empty($data['name'])) ? $data['name'] : null;
		$this->visible = (!empty($data['visible'])) ? $data['visible'] : null;
		$this->summary  = (!empty($data['summary'])) ? $data['summary'] : null;
		$this->module= array();
		if (!empty($data['modules'])){
			//$json = json_decode($data['modules'],true);
			$json =$data['modules'];
			
			foreach ($json as $res) {
				
				$content = new MoodleModule();
				$content->exchangeArray($res);
				array_push($this->module, $content);
			}
		}
		else{
			$this->module=null;
		}
	}
	
	
}
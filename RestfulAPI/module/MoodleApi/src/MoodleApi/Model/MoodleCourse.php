<?php
namespace MoodleApi\Model;

class MoodleCourse
{
	public $id;
	public $sid;
	public $shortname;
	public $categoryid;
	public $categorysortorder;
	public $fullname;
	public $idnumber;
	public $summary;
	public $summaryformat;
	public $format;
	public $showgrades;
	public $newsitems;
	public $startdate;
	public $numsections;
	public $maxbytes;
	public $showreports;
	public $visible;
	public $hiddensections;
	public $groupmode;
	public $groupmodeforce;
	public $defaultgroupingid;
	public $timecreated;
	public $timemodified;
	public $enablecompletion;
	public $completionnotify;
	public $lang;
	public $forcetheme;
	public $courseformatoptions;
	
		
	public function exchangeArray($data)
	{
		$this->id     = (!empty($data['id'])) ? $data['id'] : null;
		$this->sid     = (!empty($data['id'])) ? $data['id'] : null;
		$this->shortname = (!empty($data['shortname'])) ? $data['shortname'] : null;
		$this->categoryid  = (!empty($data['categoryid'])) ? $data['categoryid'] : null;
		$this->categorysortorder     = (!empty($data['categorysortorder'])) ? $data['categorysortorder'] : null;
		$this->fullname = (!empty($data['fullname'])) ? $data['fullname'] : null;
		$this->idnumber  = (!empty($data['idnumber'])) ? $data['idnumber'] : null;
		$this->summary     = (!empty($data['summary'])) ? $data['summary'] : null;
		$this->summaryformat = (!empty($data['summaryformat'])) ? $data['summaryformat'] : null;
		$this->format  = (!empty($data['format'])) ? $data['format'] : null;
		$this->showgrades     = (!empty($data['showgrades'])) ? $data['showgrades'] : null;
		$this->newsitems = (!empty($data['newsitems'])) ? $data['newsitems'] : null;
		$this->startdate  = (!empty($data['startdate'])) ? $data['startdate'] : null;
		$this->numsections     = (!empty($data['numsections'])) ? $data['numsections'] : null;
		$this->maxbytes = (!empty($data['maxbytes'])) ? $data['maxbytes'] : null;
		$this->showreports  = (!empty($data['showreports'])) ? $data['showreports'] : null;
		$this->visible     = (!empty($data['visible'])) ? $data['visible'] : null;
		$this->hiddensections = (!empty($data['hiddensections'])) ? $data['hiddensections'] : null;
		$this->groupmode  = (!empty($data['groupmode'])) ? $data['groupmode'] : null;
		$this->groupmodeforce     = (!empty($data['groupmodeforce'])) ? $data['groupmodeforce'] : null;
		$this->defaultgroupingid = (!empty($data['defaultgroupingid'])) ? $data['defaultgroupingid'] : null;
		$this->timecreated  = (!empty($data['timecreated'])) ? $data['timecreated'] : null;
		$this->timemodified     = (!empty($data['timemodified'])) ? $data['timemodified'] : null;
		$this->enablecompletion = (!empty($data['enablecompletion'])) ? $data['enablecompletion'] : null;
		$this->completionnotify  = (!empty($data['completionnotify'])) ? $data['completionnotify'] : null;
		$this->lang     = (!empty($data['lang'])) ? $data['lang'] : null;
		$this->forcetheme = (!empty($data['forcetheme'])) ? $data['forcetheme'] : null;
		$this->courseformatoptions  = (!empty($data['courseformatoptions'])) ? $data['courseformatoptions'] : null;
	}
}
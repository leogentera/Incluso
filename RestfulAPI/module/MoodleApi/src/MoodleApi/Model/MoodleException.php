<?php
namespace MoodleApi\Model;

class MoodleException
{
	public $exception;
	public $errorcode;
	public $message;
	public $debuginfo;
	

	public function exchangeArray($data)
	{
		$this->exception     = (!empty($data['exception'])) ? $data['exception'] : null;
		$this->errorcode     = (!empty($data['errorcode'])) ? $data['errorcode'] : null;
		$this->message     = (!empty($data['message'])) ? $data['message'] : null;
		$this->debuginfo     = (!empty($data['debuginfo'])) ? $data['debuginfo'] : null;
	}
}
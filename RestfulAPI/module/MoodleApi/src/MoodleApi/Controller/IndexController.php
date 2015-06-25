<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/MoodleApi for the canonical source repository
* @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
*/

namespace MoodleApi\Controller;

use Zend\View\Model\JsonModel;

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
    	$this->_helper->json(array('data' => "Welcome to the Zend Framework Moodle API example"));
        //return new JsonModel(array('data' => "Welcome to the Zend Framework Moodle API example"));
    }
}
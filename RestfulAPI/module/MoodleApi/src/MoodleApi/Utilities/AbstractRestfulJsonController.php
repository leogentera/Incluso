<?php
namespace MoodleApi\Utilities;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Http\Response;

class AbstractRestfulJsonController extends AbstractRestfulController
{
	
    private $config;
    public function getConfig() {
    	if ($this->config == null) {
    		$this->config = $this->getServiceLocator()->get('config');
    	}
    	return $this->config;
    }
    
    protected function methodNotAllowed()
    {
        $this->response->setStatusCode(405);
        throw new \Exception('Method Not Allowed');
    }

    # Override default actions as they do not return valid JsonModels
    public function create($data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }

    public function deleteList()
    {
        return $this->methodNotAllowed();
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function getList()
    {
        return $this->methodNotAllowed();
    }

    public function head($id = null)
    {
        return $this->methodNotAllowed();
    }

    public function options()
    {
        return $this->methodNotAllowed();
    }

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function replaceList($data)
    {
        return $this->methodNotAllowed();
    }

    public function patchList($data)
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }
    
    private function hasToken() {
    	$request = $this->getRequest();
    	if (isset($request->getCookie()->MOODLE_TOKEN)) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    private function generateToken() {
    	$url = $this->getConfig()['TOKEN_GENERATION_URL'];
    	$url = sprintf($url, 'Admin', 'administrator', $this->getConfig()['MOODLE_SERVICE_NAME']);
    	$response = file_get_contents($url);
    	$json = json_decode($response,true);
    	setcookie('MOODLE_TOKEN', $json['token'], time() + 3600, '/',null, false); //the true indicates to store only if there´s a secure connection
    
    	return $json['token'];
    }
    
    public function getToken() {
    	$token = '';
    	$request = $this->getRequest();
    	if ($this->hasToken()) {
    		$token = $request->getCookie()->MOODLE_TOKEN;
    	} else {
    		$token = $this->generateToken();
    	}
    	return $token;
    }
}
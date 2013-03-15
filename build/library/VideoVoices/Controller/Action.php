<?php

class VideoVoices_Controller_Action extends Zend_Controller_Action
{
    public function init()
    {
      $config = Zend_Registry::get('config', $config);
		$this->view->addHelperPath('VideoVoices/View/Helper', 'VideoVoices_View_Helper');
    	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }
    
    public function _getID()
    {
        $id = $this->getRequest()->getParam('id', null);
       		
       	if (is_null($id))
       	{
       		$className = explode('_', get_class($this));
       		$this->_flashMessenger->addMessage('No ID specified for ' . $className);
       		//$this->_redirect('/' . strtolower($className));
       	}
       	
       	return $id;
    }
    
    public function preDispatch()
    {
		parent::preDispatch();
    	$request = $this->getRequest();

    	if (!Zend_Auth::getInstance()->hasIdentity()) {
    		if ($request->getPathInfo() != '/admin/login') {
	    		$url = '/admin/login?redirect='.urlencode($request->getPathInfo());
    		}
    		if ($request->getPathInfo() == '/admin/logout') {
    			$url = '/admin/login';
    		}
    		if (!empty($url)) {
	    		$this->_redirect($url);
	    	}
    	}
    	else {
//    		var_dump($request);exit;
//    		if ($request->getParam('redirect'))
    	}
    	
    	$acl = Zend_Registry::get('acl');
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$storage = $auth->getStorage()->read();
			$role = $storage->role;
			if (!$acl->isAllowed($role, $request->getControllerName(), $request->getActionName())) {
				die('Access Denied');
			}
			$this->view->currentUser = $storage;
		}
    }

    public function postDispatch()
	{
        $this->view->messages = array_merge($this->_flashMessenger->getMessages(), $this->_flashMessenger->getCurrentMessages());
    	$this->_flashMessenger->clearCurrentMessages();
	}
}

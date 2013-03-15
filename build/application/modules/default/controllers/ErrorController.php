<?php

class ErrorController extends VideoVoices_Controller_FrontAction //Zend_Controller_Action
{
	
	public function init(){
		
		parent::init();
		
		//$this->view->config = Zend_Registry::get('config');
		//$this->view->env = APPLICATION_ENV;

		//META
		$this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
		$this->view->headMeta()->appendHttpEquiv('UA-Compatible', 'IE=edge,chrome=1');
		$this->view->headMeta()->appendName('author' , '');
		$this->view->headMeta()->appendName('description' , '');
		$this->view->headMeta()->appendName('viewport' , 'width=device-width, initial-scale=1.0');
		
		//FAVICONS	
		$this->view->headLink()->headLink(array('rel' => 'shortcut icon', 'href' => $this->view->assetUrl.'images/icons/favicon.ico') );
		$this->view->headLink()->headLink(array('rel' => 'apple-touch-icon', 'href' => $this->view->assetUrl.'images/icons/apple-touch-icon.png') );
		
		//CUSTOM CSS
		$this->view->headLink()->appendStylesheet($this->view->assetUrl . "css/style.css" );
		
		//JS
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/jquery-1.7.1.js');
				
		
		$this->view->headTitle('Video Voices');
		$this->view->headTitle()->setSeparator(' - ');
	
	}
	
    public function errorAction()
    {	
    
    	$errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }

		$this->_helper->layout->disableLayout();
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}


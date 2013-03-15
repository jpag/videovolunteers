<?php

class IndexController extends VideoVoices_Controller_FrontAction
{
	/**
	 * Static config from application.ini
	 * @var Zend_Config
	 */
	protected $config;
	 
	/**
	 * Settings defined in admin section
	 * @var Zend_Db_Row
	 */
	protected $settings;

	public function init()
	{
		error_reporting(E_ALL);
		parent::init();

		$this->view->config = Zend_Registry::get('config');
		//$this->view->env = APPLICATION_ENV; //is this needed?
		
		$nodeUrl = $this->view->config->nodeUrl;
				
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

	public function indexAction()
	{
		//DB DEMO
		//$vidm = new VideoVoices_Model_Video_Table();
		//$videos = $vidm->getSince('hi');
		
		$this->facebookCheckLogin();		
		
		//CSS
		$this->view->headLink()->prependStylesheet($this->view->assetUrl . "css/jqueryui/themes/custom/jquery.ui.all.css" );
		$this->view->headLink()->prependStylesheet($this->view->assetUrl . "css/counter.css" );
		$this->view->headLink()->prependStylesheet($this->view->assetUrl . "css/fonts.css" );
		
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/ui/jquery.ui.core.min.js');
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/ui/jquery.effects.core.min.js');
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/ui/jquery.ui.widget.min.js');
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/ui/jquery.ui.mouse.min.js');
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/ui/jquery.ui.slider.min.js');
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/ui/jquery.ui.button.min.js');
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/jquery.easing.1.3.min.js');
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/ember-0.9.4.min.js');
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/flipcounter.min.js');
		
		//CUSTOM JS
		if( $this->view->config->debug == true){
			$this->view->headScript()->appendFile($this->view->assetUrl . 'js/app.js');
			
		}else{
			$this->view->headScript()->appendFile($this->view->assetUrl . 'js/app.min.js');
		}
		
		//GET number of users and total money made so far (actually number of friends viewed.. and math it.)
		//this calls are cached in the model view.
		$userTable = new VideoVoices_Model_User_Table();
		$this->view->totalNumberPeople = $userTable->getUserCount();
		
		$postTable = new VideoVoices_Model_Posts_Table();
		$this->view->totalMoneyRaised = $postTable->getTotalRaised();
		
		//$this->view->GA = $this->view->config->googleAnalytics;
	}

	
	public function facebookCheckLogin(){
		//FACEBOOK check:
			require_once (__DIR__ . '/../../../../library/Facebook/facebook.php');
			$fbconfig = array();
			
			$fbconfig['appId'] = $this->view->config->facebookAppId; 	//$this->config->get('facebookAppId');
			$fbconfig['secret'] = $this->view->config->facebookAppSecret; //$this->config->get('facebookAppSecret');
			$fbconfig['fileUpload'] = false;
			$facebook = new Facebook($fbconfig);
			
			//$access_token = $facebook->getAccessToken();
			$uid = $facebook->getUser();
			
			if ($uid) {
				 try {
						$user_profile = $facebook->api('/me','GET');
				        
				        $userTable = new VideoVoices_Model_User_Table();
				        $FBreturn = $userTable->getUser( array("fbid"=>$uid) );
				        
				        if( $FBreturn ) $this->view->FB = $FBreturn;
				        
			      } catch(FacebookApiException $e) {
				        // If the user is logged out, you can have a 
				        // user ID even though the access token is invalid.
				        // In this case, we'll get an exception, so we'll
				        // just ask the user to login again here.

				        //$login_url = $facebook->getLoginUrl(); 
				        //echo 'Please <a href="' . $login_url . '">login.</a>';
				        //error_log($e->getType());
				        //error_log($e->getMessage());
				 }   		
			}else{
		
			}
		
	}
	
	/*
	public function checkmyerrorsAction()
	{
		$this->view->results = VideoVoices_Model_Entrant_Table::getAllImages();
	}
	*/
}

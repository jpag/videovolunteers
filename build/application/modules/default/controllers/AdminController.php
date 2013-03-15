<?php

class AdminController extends VideoVoices_Controller_FrontAction
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
	
	protected $request;
	
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
		$this->view->headLink()->appendStylesheet($this->view->assetUrl . "css/adminstyle.css" );
		$this->view->headLink()->prependStylesheet($this->view->assetUrl . "css/jqueryui/themes/custom/jquery.ui.all.css" );
		
		$this->view->headLink()->prependStylesheet($this->view->assetUrl . "css/fonts.css" );
		
		//JS
		$this->view->headScript()->appendFile($this->view->assetUrl . 'js/libs/jquery-1.7.1.js');
		


		if (APPLICATION_ENV == 'production' || APPLICATION_ENV == 'staging') {//} || APPLICATION_ENV == 'test') {
			$this->view->headScript()->appendFile($this->view->assetUrl . 'js/cms.min.js');
		}else{
			$this->view->headScript()->appendFile($this->view->assetUrl . 'js/cms.js');
		}
		
		$this->view->headTitle('Video Voices');
		$this->view->headTitle()->setSeparator(' - ');
		
		
		$this->request = $this->getRequest();
		if( $this->request->getParam('format') != 'json' ){ 
			$this->_helper->layout->setLayout('admin');
		}else{
			
			$this->_helper->contextSwitch()
			             ->addActionContext('correspondents', 'json')
			             ->addActionContext('videos', 'json')
			             ->addActionContext('update', 'json')
			             ->addActionContext('delete', 'json')
			             ->addActionContext('add', 'json')
			             ->initContext();             
		};
		
		$this->view->action = $this->request->getParam('action');
		$this->view->page = $this->request->getParam('page');	
		$this->view->issueVal = array("Education","Corruption","Justice","Woman's Rights","Environment");
		$this->view->issueValAbreviated = array("Edu","C","J","W","Ev");
	}

	public function indexAction()
	{
		$this->_helper->layout->setLayout('admin');	             
	}
	
	
	public function videosAction(){
		$videos = new VideoVoices_Model_Video_Table();
		$this->view->list = $videos->getVideos( $this->request->getParam('page') , $this->view->config->cms->numberPerPage );
		
		$correspondents = new VideoVoices_Model_Correspondent_Table();
		$this->view->correspondentList = $correspondents->getCorrespondentMin( );
	}
	
	public function correspondentsAction(){
		$correspondents = new VideoVoices_Model_Correspondent_Table();
		$this->view->list = $correspondents->getCorrespondents( $this->request->getParam('page') , $this->view->config->cms->numberPerPage );
	}
	
	public function updateAction(){
		
		$this->view->id = $this->request->getParam('id');
		
		if( $this->request->getParam('page') == 'correspondents' ){
			$user = array(
				'name' => $this->request->getParam('name'),
				'twitterhandle' => $this->request->getParam('twitterhandle'),
				'fbhandle' => $this->request->getParam('fbhandle'),
				'address' => $this->request->getParam('address')
			);
			$correspondents = new VideoVoices_Model_Correspondent_Table();
			$this->view->response = $correspondents->updateCorrespondent( $this->view->id , $user );
			$this->view->user = $user;
		}else if( $this->request->getParam('page') == 'videos' ){
			
			$video = array(
				'title' => $this->request->getParam('title'),
				'youtube_id' => $this->request->getParam('youtubeid'),
				'correspondent_id' => $this->request->getParam('correspondent'),
				'posted_date' => $this->request->getParam('posted'),
				'issues' => $this->request->getParam('issues')
			);
			
			$videos = new VideoVoices_Model_Video_Table();
			$this->view->response = $videos->updateVideo( $this->view->id , $video );
			$this->view->video = $video;
			
		}else{
			$this->view->error = 'no valid page defined';
		}
	}
	
	public function deleteAction(){
		$this->view->id=$this->request->getParam('id');
		if( $this->request->getParam('page') == 'correspondents' ){
			$correspondents = new VideoVoices_Model_Correspondent_Table();
			$this->view->response = $correspondents->deleteCorrespondent( $this->request->getParam('id') );
		}else if( $this->request->getParam('page') == 'videos' ){
			$videos = new VideoVoices_Model_Video_Table();
			$this->view->response = $videos->deleteVideo( $this->request->getParam('id') );
		}else{
			$this->view->error = 'no valid page defined';
		}
	}
	
	public function addAction(){
		if( $this->request->getParam('page') == 'correspondents' ){
			
			$user = array(
				'name' => $this->request->getParam('name'),
				'twitterhandle' => $this->request->getParam('twitterhandle'),
				'fbhandle' => $this->request->getParam('fbhandle'),
				'address' => $this->request->getParam('address')
			);
			
			$correspondents = new VideoVoices_Model_Correspondent_Table();
			$this->view->response = $correspondents->addNewCorrespondent( $user );
			$this->view->user = $user;
			
		}else if( $this->request->getParam('page') == 'videos' ){
			
			$video = array(
				'title' => $this->request->getParam('title'),
				'youtube_id' => $this->request->getParam('youtubeid'),
				'correspondent_id' => $this->request->getParam('correspondent'),
				'posted_date' => $this->request->getParam('posted'),
				'issues' => $this->request->getParam('issues')
			);
			
			$videos = new VideoVoices_Model_Video_Table();
			$this->view->response = $videos->addNewVideo( $video );
			$this->view->video = $video;
			
			
		}else{
			$this->view->error = 'no valid page defined';
		}
	}
	
	
}

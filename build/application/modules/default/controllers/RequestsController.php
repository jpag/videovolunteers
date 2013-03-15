<?php

class RequestsController extends VideoVoices_Controller_FrontAction
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
		
		$this->_helper->contextSwitch()
		             ->addActionContext('recentvideos', 'json')
		             ->addActionContext('userupdate', 'json')
		             ->addActionContext('userstat', 'json')
		             ->initContext();

	}

	public function indexAction(){

	}

	public function recentvideosAction(){
			$this->view->recentvideos = array(
				"error" => 'does not exist'
			);
			
			$recentVideos = new VideoVoices_Model_Video_Table();
			$this->view->recentvideos = $recentVideos->getRecentVideos();//getRecentVideos();
	}

	public function userupdateAction(){
		$this->request = $this->getRequest();
		$uid = $this->request->getParam('fbid');
		
		if( isset( $uid ) ){
			$issues = $this->request->getParam('issues');
			if( !isset( $issues ) ){
			 	$issues = '00000';
			}
			
			$posts = $this->request->getParam('posts');
			if( !isset($posts ) ){
			 	$posts = 0;
			}

			$userInfo = array(
				'fbid' => $uid,
				'issues'=> $issues,
				'frequency' =>$posts
				// 'totalfriends'=>$numOfFriends
			);
			
			$userTable = new VideoVoices_Model_User_Table();
			$this->view->result = $userTable->getUser( $userInfo );
		}else{
			$this->view->result = 0;
		}
	}

	//this pretty much isn't used anymore as this is managed by the postcontroller now.
	public function userstatAction(){
		$this->request = $this->getRequest();
		$uid = $this->request->getParam('uid');
		
		$userTable = new VideoVoices_Model_User_Table();
		$FBreturn = $userTable->getUser( array("fbid"=>$uid) );

		if( $FBreturn ){
			
			//add up all the posts that match this user together for total posts and friend shares.
			$postsTable = new VideoVoices_Model_Posts_Table();
			$totalsResult = $postsTable->getTotalsForUser( $FBreturn['id'] );

			//Zend_Debug::dump($totalsResult);

			$FBreturn["totalposts"] = $totalsResult["totalposts"];
			$FBreturn["moneyraised"] = $totalsResult["moneyraised"];
			$this->view->message = $FBreturn;

			//generate email?

		}else{
			$this->view->message = 'No User found';
		}
	}



}

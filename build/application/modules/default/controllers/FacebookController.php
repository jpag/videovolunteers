<?php

class FacebookController extends VideoVoices_Controller_FrontAction
{
	/**
	 * Static config from application.ini
	 * @var Zend_Config
	 */
	protected $config;
	 
	protected $facebook;
	
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
		

		//FACEBOOK SETUP
		require_once (__DIR__ . '/../../../../library/Facebook/facebook.php');
		$this->config = array();
		$this->config['appId'] = $this->view->config-> get('facebookAppId');
		$this->config['secret'] = $this->view->config->get('facebookAppSecret');
		$this->config['fileUpload'] = false;
		$this->facebook = new Facebook($this->config);

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
		

		$this->_helper->contextSwitch()
		             ->addActionContext('updatetoken', 'json')
		             ->addActionContext('user', 'json')
		             ->initContext();

	}

	public function indexAction()
	{
		
		//echo $this->view->config->httpprotocol;
		$thisURL = $this->view->config->httpprotocol.'://';
		$thisURL .= $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];

		//echo $thisURL;
		//die;

		$uid = $this->facebook->getUser();

		if (isset($_GET["code"]) && $uid ) {
			//if success'd login
			$state = $_GET["state"];
			$code = $_GET["code"];
			try {
				$this->view->user_profile = $this->facebook->api('/me','GET');
			
				//get the auth token
				$token_url = $this->view->config->httpprotocol."://graph.facebook.com/oauth/access_token?" . "client_id=" . $this->config['appId'] . "&redirect_uri=" . urlencode($thisURL) . "&client_secret=" . $this->config['secret'] . "&code=" . $code;
				$response = @file_get_contents($token_url);
				$params = null;
				parse_str($response, $params);
				
				$uid = $this->facebook->getUser();
				$access_token = $this->facebook->getAccessToken();
				
				// FQL documentation http://developers.facebook.com/docs/reference/fql/	
				//THIS TAKES A WHILE TO CALL and RETURN
				
				$fql_query_url = $this->view->config->httpprotocol.'://graph.facebook.com//fql?q='
								.'SELECT+friend_count+FROM+user+WHERE+uid+='.$uid.'&'.$access_token;
								
				$fql_query_result = file_get_contents($fql_query_url);
				$fql_query_obj = json_decode($fql_query_result, true);
				$numOfFriends = $fql_query_obj["data"][0]["friend_count"];	
				
				//GET extended access token:
				//$extendsAccessTokenUrl = $this->view->config->httpprotocol."://graph.facebook.com/oauth/access_token?client_id=".$this->config['appId'];
				$extendsAccessTokenUrl = "https://graph.facebook.com/oauth/access_token?client_id=".$this->config['appId'];
					$extendsAccessTokenUrl .= "&client_secret=".$this->config['secret'];
					$extendsAccessTokenUrl .= "&grant_type=fb_exchange_token&fb_exchange_token=".$access_token;
				$responseExtendedToken = @file_get_contents($extendsAccessTokenUrl);

				parse_str($responseExtendedToken, $paramsEAT);

				if( isset($paramsEAT['expires']) ){
					$access_token_expiration = $paramsEAT['expires'];
				}else{
					$access_token_expiration = 0;
				}

				if( isset($paramsEAT['access_token']) ){
					$access_token_extended = $paramsEAT['access_token'];
				}else{
					$access_token_extended = 0;
				}

				//Zend_Debug::dump($paramsEAT);
				//Zend_Debug::dump( $this->view->user_profile["email"] );

				$issues = '00000';
				if( isset($_GET['issues']) ){
					$issues =  $_GET['issues'];
				}
				
				$posts = 0;
				if( isset($_GET['posts']) ){
					$posts = $_GET['posts'];
				}
				
				$userInfo = array(
					'fbid' => $uid,
					'issues'=> $issues,
					'frequency' =>$posts,
					'totalfriends'=>$numOfFriends,
					'access_token' => $access_token_extended,
					'access_token_expiration'=> $access_token_expiration
					//SHOULD WE SAVE THEIR EMAIL probably not...
				);
				
				//write to DB here
				$userTable = new VideoVoices_Model_User_Table();
				$user = $userTable->getUser($userInfo);

				//$user['data'] = $this->view->user_profile;

				$this->view->userJson = json_encode($user);	
				$this->view->access_token = $access_token;
				
				//force post on first time user:
				$fbPostUrl = $this->view->config->httpprotocol.'://';
				$fbPostUrl .= $_SERVER['HTTP_HOST'] . '/post/'.$uid; 
				$response = @file_get_contents($fbPostUrl);
				//echo $fbPostUrl;
				//echo $response;
				//die;

			} catch (FacebookApiException $e) {
    			//not logged in
    			echo 'An error occurred using the current access token. Try <a href="/facebook/logout">logging out</a> and logging back in to complete the process';
  			}

		}elseif(isset($_GET['error'])) {
			//if error'd login
			
			echo $_GET['error'] . '<br/>';
			echo $_GET['error_reason'] . '<br/>';
			echo $_GET['error_description'] . '<br/>';


			// use different layout script with this action:
        	//$this->_helper->layout->setLayout('error');

		} else {
			//new attempt, redirect to facebook
			$params = array(	
							'scope' => $this->view->config->fbScope, 
							'redirect_uri' => $thisURL,
							'display' => 'popup'
						);
							
			$loginUrl = $this->facebook -> getLoginUrl($params);
			//echo $loginUrl;
			header('Location: ' . $loginUrl);
		}

	}
	
	public function updatetokenAction(){
		
		if ( $this->facebook ){
      		 try {
      		 	
      		 	$user_profile = $this->facebook->api('/me','GET');
   	 			
   	 			$uid = $this->facebook->getUser();
   	 			if ($uid) {
   	 				//$this->view->me = $me;
   	 				
       				$access_token = $this->facebook->getAccessToken();
					//GET extended access token:
					//$extendsAccessTokenUrl = $this->view->config->httpprotocol."://graph.facebook.com/oauth/access_token?client_id=".$this->config['appId'];
					$extendsAccessTokenUrl = "https://graph.facebook.com/oauth/access_token?client_id=".$this->config['appId'];
					$extendsAccessTokenUrl .= "&client_secret=".$this->config['secret'];
					$extendsAccessTokenUrl .= "&grant_type=fb_exchange_token&fb_exchange_token=".$access_token;

					$responseExtendedToken = @file_get_contents($extendsAccessTokenUrl);
					$this->view->url = $extendsAccessTokenUrl;
					$this->view->response = $responseExtendedToken;

					parse_str($responseExtendedToken, $paramsEAT);
					if( isset($paramsEAT['expires']) ){
						$access_token_expiration = $paramsEAT['expires'];
					}else{
						$access_token_expiration = 0;
					}

					if( isset($paramsEAT['access_token']) ){
						$access_token_extended = $paramsEAT['access_token'];
					}else{
						$access_token_extended = 0;
					}

					//write to DB here
					if( $access_token_expiration==0 && $access_token_extended==0 ){
						$this->view->error = 'invalid access token and or expiration';
					}else{
						$userTable = new VideoVoices_Model_User_Table();
						$user = $userTable->updateAccessToken($uid, $access_token_extended, $access_token_expiration );
						$this->view->result = $user;
					}

					//display json result // largely for debugging
					$this->view->access_token = $access_token;
					$this->view->access_token_extended = $access_token_extended;
					$this->view->access_token_expiration = $access_token_expiration;
					$this->view->new_expiration = date('l jS \of F Y h:i:s A' , time()+$access_token_expiration );
					$this->view->fbid = $uid;
					
    			}else{
    				$this->view->error = 'user is not logged in';	
    			}
  			} catch (FacebookApiException $e) {
    			//not logged in
    			$this->view->error = 'user is not logged in';
  			}
  		}
	}
	
	public function logoutAction(){
		echo '<div style="width:200px; margin:auto; position:relative; text-align:center; padding:10px; background:#FFFFFF; margin-top:10px;">';
		echo '<a href="'.$this->facebook->getLogoutUrl().'" target="_self" >Logout</a><br><br>';
		echo '<a href="../">Back</a></div>';
	}




}

<?php

class PostController extends VideoVoices_Controller_FrontAction
{
	/**
	 * Static config from application.ini
	 * @var Zend_Config
	 */
	protected $config;
	
	protected $facebook;

	public function init()
	{
		error_reporting(E_ALL);
		parent::init();

		$this->view->config = Zend_Registry::get('config');
		
		$this->_helper->layout->setLayout('cron');
		//$this->_helper->layout->disableLayout();
		//$this->_helper->contextSwitch()
		//	             ->addActionContext('index', 'json')
		//	             ->initContext();
	}

	public function indexAction()
	{

		# get a list of all videos in the last 30 days, order by recency and have issues available for each one
		$videoModel = new VideoVoices_Model_Video_Table();
		$recentVideos = $videoModel->getLastVideosAdded();
		//Zend_Debug::dump($recentVideos);
		if( count($recentVideos) == 0 ){
			echo ' warning no videos found';
			die;
		}else{
			echo ' number of videos: '. count($recentVideos);
		}

		$this->request = $this->getRequest();

		$userTable = new VideoVoices_Model_User_Table();

		if( $this->request->getParam('fbid') ){
			//just post for this user (new user);
			$user = $userTable->getUser(array('fbid'=> $this->request->getParam('fbid') ) );
			
			$user['posted_date'] = 0;
			$user["userfrequency"] = 1;
			
			//var_dump($user);
			
			$usersToPost[] = $user;
		}else{
			# get a list of users who are active and should be posted to today
			$usersToPost = $userTable->getUsersToPostTo();
			
			//Zend_Debug::dump($usersToPost);
			echo '<br><br>';

		}

		if( count( $usersToPost ) == 0 ){
			echo ' warning no users found';
			die;
		}else{
			echo ' USER count: ' . count( $usersToPost );
		}

		//FACEBOOK SETUP
		require_once (__DIR__ . '/../../../../library/Facebook/facebook.php');
		$this->config = array();
		$this->config['appId'] = $this->view->config->get('facebookAppId');
		$this->config['secret'] = $this->view->config->get('facebookAppSecret');
		$this->config['fileUpload'] = false;
		$this->facebook = new Facebook($this->config);

		/*
		for each of those users:
		run the following algorithm:
		*/

		$this->view->date = date("Y-m-d");

		foreach( $usersToPost as $user ){
			// user's next post should be now or in the past, not in the future.
			echo '<div style="background-color:#EEE; padding:10px; border:1px solid #DDD; margin:10px;" >USER : ';

			$user_profile = null; //clear the variable if it is holding anything from the previous user;
			$video_to_post = null;

			echo $user['id'] . ' - ' . $user['fbid'];
			Zend_Debug::dump($user);	

			if( $user['nextpost'] <= date("Y-m-d") ){

				//get JUST a list of IDs from the recentvideo list
				$videoIDs = array();
				foreach($recentVideos as $vid) { $videoIDs[] =$vid['id']; }
				
				//CHECK if a user has already posted any of these recentvideos IDs:
				$postModel = new VideoVoices_Model_Posts_Table();
				$videosPostedAlready = $postModel->findMatchingPosts($user["id"], $videoIDs );
				//Zend_Debug::dump($videosPostedAlready);
				
				/* 
				loop through the videos:
					check to make sure video was not posted
						if a video is OLDER then a user's LAST post
							pick that video 
								if it ends up being used: 
								a) it was never picked before 
								b) there will be no videos that match issues that are newer
						else video is NEWER then user's Last POST
							check for a matching issue
							break video selection. "prefect match"
				*/

				//MAKE METHOD
				foreach ($recentVideos as $video ) {
					//has this video been posted or not?
					
					//http://php.net/manual/en/function.array-search.php
					//This function may return Boolean FALSE, but may also return a non-Boolean value which evaluates to FALSE. Please read the 
					//section on Booleans for more information. Use the === operator for testing the return value of this function.

					if( array_search($video['id'], $videosPostedAlready) === false ){

						if ( $user['posted_date'] >= $video['date_default'] ){
							//VIDEO is OLDER THEN POST
							//so pick this one that doesn't match an existing one already posted by user.
							//BACKUP plan to continue posting videos if the issues don't match up.
							$video_to_post = $video;
						}else{	
							//VIDEO posted date is newer
							
							//http://php.net/manual/en/language.operators.bitwise.php
							//check BITWISE to see if both issues_selected and issues have a matching BIT = 1

							//CHECK FOR MATCHING ISSUES
							if( ( $video["issues_selected"] & $user["issues"]) > 0 ){
								echo '<br> PERFECT MATCH FOUND ' . $video['id'];
								$video_to_post = $video;
								//PERFECT SCENARIO -> VIDEO is NEWER, and MATCHES ISSUES
								break 1; //BREAK ONLY THE foreach looping of the recentVideos
							}
						}
					}else{
						echo "<br> ".$video['id']." -this video already posted by user.<br>";
					}
				}
				
				//CHECK FOR ACCESS TOKEN and IF there is a VIDEO TO POST.
				if( $user["access_token"]){
					if( isset($video_to_post) ){
						
						//Zend_Debug::dump($video_to_post);
						echo $video_to_post['id'] . ' - ' . $video_to_post['title'];
						
						//create post array:
						//http://developers.facebook.com/docs/reference/api/post/
						$post = array(
										'access_token' => $user["access_token"], 
										'message' => 'I donated a part of my Facebook News Feed to help journalists in the developing world like this one.' ,
										'link'=>'http://www.youtube.com/watch?v='.$video_to_post['ytid']
										);  

						try{

							/*
							POST the selected video to the user's wall
							*/  
							$result = $this->facebook->api('/'.$user['fbid'].'/feed','POST',$post);
							echo ' SUCCESSFULLY POSTED----';
							
							/*
							INSERT a NEW 'posts' row in the db for that user
							*/
							$posted = array(
									"uid" => $user["id"],
									"vid" => $video_to_post["id"],
									"date" => date("Y-m-d"),
									"totalfriends" => $user["totalfriends"]
								);

							//WRITE TO DATABASE THE POST:
							$videoPosted = $postModel->addPost($posted);

						}catch(Exception $e ){
							echo ' ERROR problem found when posting to the wall '. $e; //->getMessage()
						}

						/*
						COMPARE ACCESS TOKEN: the expiration date to the estimated NEXT post, 
							check for expiration of user's FB token here... 
							if coming up soon email warn
						*/

						$daysFromNextPost = 30 - $user['userfrequency'];
						if( $daysFromNextPost < 1 ){
							$daysNext = " +1 day";
						}else{
							$daysNext = " +".$daysFromNextPost." days";
						}
						
						$expirationDate = date('Y-m-d' , strtotime($user['fb_token_expires']) );
						$followingPost = date("Y-m-d" , strtotime( date("Y-m-d").$daysNext ) );
						
						if( $expirationDate <= $followingPost ){
							echo '<br> -EXPIRES SOON! ' . $expirationDate . ' <= ' . $followingPost;
							try{
								
								echo "<br> User's Email: ";
								$user_profile = $this->facebook->api('/'.$user['fbid'],'GET');
								
								//Zend_Debug::dump($user_profile);
								echo $user_profile['name'] . ' - ' . $user_profile["email"] ;
								$this->emailUserTokenIsExpiring( $user_profile["email"] , $user_profile['name'] );

							}catch(Exception $e){
								echo '<br> error on trying to access users FB/api/me profile: '.$e;
							}
						}

					}else{
						echo '<br> NO VIDEO found';
					}	
				}else{
					//S.O.L. //no access token no way of notifying them.
					echo '<br> NO VALID ACCESS TOKEN';
				}
			}else{
				//should already be filtered out by the table.php USER query
				echo' <br>- Users next post is still later - not ready to post.';
			}
			echo '</div>';
		}
		
		/*
		done
		*/

	}
	
	public function emailUserTokenIsExpiring($email, $name){

		$hyperlinkCSS = "style='color:#ea5724; text-decoration:none;'";
		$bodyStyle = "<body style='background:#f4f4f4; color:#333333; font-size:12px; padding:20px; width:725px;'>";
		$highlightCSS = "<span style='color:#0b747e';>";

		$body = $bodyStyle."<img src='http://".$this->view->config->webhost."/assets/images/email/toplogo.jpg' width='183' height='154' /><br>";
		$body .= '<br/><br/><span style="font-size:16px;">'.$name.',</span><br/><br>';
		$body .= "Due of Facebook Platform Policy we cannot continue to automatically post videos from journalists in the developing world to your Facebook News Feed after 60. To continue with your contribution to Video Volunteers we simply need you to click on the following link. You can adjust your contribution settings at any time.";
		$body .= "</body>";

		$bodyAlt = $body;
		
		$title = "Please Revisit Video Voices";
		$this->emailUser($email , $body , $bodyAlt , $title );

	}	
	
	public function emailUser($email, $bodyHtml , $bodyAlt , $title){
		require_once (__DIR__ . '/../../../../library/phpmailer/class.phpmailer.php');
		echo ' sending email...';
		// note, this is optional - gets called from main class if not already loaded
		require_once(__DIR__ . '/../../../../library/phpmailer/class.smtp.php'); 

		$mail             = new PHPMailer();

		$mail->IsSMTP();
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mail->Host       = $this->view->config->email->smtp->host;      // sets GMAIL as the SMTP server
		$mail->Port       = $this->view->config->email->smtp->port;                   // set the SMTP port

		$mail->Username   = $this->view->config->email->user;  		// GMAIL username
		$mail->Password   = $this->view->config->email->password;	// GMAIL password

		$mail->From       = $this->view->config->email->user;
		$mail->FromName   = $this->view->config->email->fromname;
		$mail->Subject    = $title;
		$mail->WordWrap   = 75; // set word wrap
		
		//$body             = $mail->getFile('contents.html');
		//$body             = eregi_replace("[\]",'',$body);
		
		$body = $bodyHtml;
		$mail->AltBody = $bodyAlt; //Text Body
		
		$mail->MsgHTML($body);

		$mail->AddReplyTo(
							$this->view->config->email->user,
							$this->view->config->email->fromname
						);

		//$mail->AddAttachment("/path/to/file.zip");             // attachment
		//$mail->AddAttachment("/path/to/image.jpg", "new.jpg"); // attachment
		
		$mail->AddAddress($email);
		$mail->IsHTML(true); // send as HTML


		if(!$mail->Send()) {
		  echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		  echo "Message has been sent";
		}
	}


}

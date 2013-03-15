<?php

class MonthlyController extends VideoVoices_Controller_FrontAction
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

		
		$this->request = $this->getRequest();

		$userTable = new VideoVoices_Model_User_Table();

		if( $this->request->getParam('fbid') ){
			$user = $userTable->getUser(array('fbid'=> $this->request->getParam('fbid') ) );
			
			$allUsers[] = $user;
		}else{
			//get a list of users who had their last email sent a month ago.
			$allUsers = $userTable->getMonthlyUsers();
			
			//Zend_Debug::dump($allUsers);
			echo '<br><br>';
		}

		if( count( $allUsers ) == 0 ){
			echo ' warning no users found';
			die;
		}else{
			echo ' USER count: ' . count( $allUsers );
		}

		//FACEBOOK SETUP
		require_once (__DIR__ . '/../../../../library/Facebook/facebook.php');
		$this->config = array();
		$this->config['appId'] = $this->view->config->get('facebookAppId');
		$this->config['secret'] = $this->view->config->get('facebookAppSecret');
		$this->config['fileUpload'] = false;
		$this->facebook = new Facebook($this->config);

		$this->view->date = date("Y-m-d");

		foreach( $allUsers as $user ){
			// user's next post should be now or in the past, not in the future.
			echo '<div style="background-color:#EEE; padding:10px; border:1px solid #DDD; margin:10px;" >USER : ';

			$user_profile = null; //clear the variable if it is holding anything from the previous user;
			echo $user['id'] . ' - ' . $user['fbid'];
			//Zend_Debug::dump($user);	
	
			//CHECK FOR ACCESS TOKEN
			if( $user["access_token"]){
				
					/*
					SEND MONTHLY EMAIL HERE
					*/

					echo '<br>MONTHLY STAT email should be no earlier then: ' . date('Y-m-d' , strtotime($user['status_email_sent']. ' +1 month ' ) );
					
					// if the status email is defined, and the current day is a month after the defined value: lets do this:
					//this check should be done already by the user table query.
					if( !isset($user['status_email_sent']) || date('Y-m-d' , strtotime($user['status_email_sent']. ' +1 month ' ) ) <= date("Y-m-d")  ){
						
						echo '<br>---SEND MONTHLY EMAIL STAT:';
						//Access Token lets try using it... it may be expired.
						try{
							echo '<br> get Email to give monthly status update: ';
							$user_profile = $this->facebook->api('/'.$user['fbid'],'GET');
						}catch(Exception $e){
							echo '<br> error on trying to acccess users email when sending them their monthly stats.';
						}
							
						
						if( isset($user_profile["email"]) ){
							
							$postModel = new VideoVoices_Model_Posts_Table();
							$totalsResult = $postModel->getTotalsForUser( $user['id'] );

							$FBreturn["totalposts"] = $totalsResult["totalposts"];
							$FBreturn["moneyraised"] = $totalsResult["moneyraised"];

							//var_dump($user_profile);

							echo $user_profile['name'] . ' - ' . $user_profile["email"];
							echo ' impressions '.$totalsResult["impressions"].' posts'.$totalsResult["totalposts"].' money '.$totalsResult["moneyraised"] . ' ';
							
							$this->emailUserMonthlyStat( 
													$user_profile['email'], 
													$totalsResult["impressions"], 
													$totalsResult["totalposts"],
													$totalsResult["moneyraised"],
													$user_profile['name'] // alternatively could use: first_name , username
													 );
							
							//WRITE TO DB the NEW  status_email_sent date was TODAY
							$userTable->updateEmailSentDate($user['fbid']);	

						}else{
							echo ' EMAIL NOT SET - can not get email<br>';
						}						
					}
				}else{
					//S.O.L. //no access token no way of notifying them.
					echo '<br> NO VALID ACCESS TOKEN';
				}
			echo '</div>';
		}
		
		/*
		done
		*/

	}
	
	public function emailUserMonthlyStat($email, $impressions, $videos, $money , $username ){
		$hyperlinkCSS = "style='color:#ea5724; text-decoration:none;'";
		$bodyStyle = "<body style='background:#f4f4f4; color:#333333; font-size:12px; padding:20px; width:725px;'>";
		$highlightCSS = "<span style='color:#0b747e';>";


		$body = $bodyStyle."<img src='http://".$this->view->config->webhost."/assets/images/email/toplogo.jpg' width='183' height='154' /><br>";
		$body .= '<br/><br/><span style="font-size:16px;">'.$username.',</span><br/><br>';
		$body .= "Thanks for donating a portion of your Facebook News Feed to Video Volunteers. To date your News Feed Donation has generated ".$highlightCSS.$impressions."</span> ";
		$body .= "impressions for ".$highlightCSS.$videos."</span> videos from journalists in the developing world,";
		$body .= " the equivalent of ".$highlightCSS."$".$money."</span> in Facebook media.";

		$body .="<br><br>You can adjust your News Feed contribution settings at anytime through our Facebook app.";
		$body .="<br><br><a href='".$this->view->config->facebookAppUrl."' target='_blank' ".$hyperlinkCSS." >UPDATE YOUR SETTINGS</a><br><br>";

		$body .= "But it's not enough for us just to get these videos seen. We need to train more journalists to report on issues that would otherwise go unheard. A donation of $500 supports and trains a Video Volunteers community correspondent for an entire year.";
		$body .="<br><br><a href='http://www.videovolunteers.org/support-us' target='_blank' ".$hyperlinkCSS.">DONATE MONEY</a><br><br>";
		$body .= "Sincerely, <br><br><span style='font-size:16px;'>Jessica Mayberry</span><br>Founding Director, Video Voulenteers<br/><br/>";
		$body .="<img src='http://".$this->view->config->webhost."/assets/images/email/bottomlogo.jpg' width='31' height='25' /></body>";


		//strip all BR ?
		$bodyAlt = $body;

		$title = "Video Voices Monthly Update";

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

<?php

/**
 * Table for Users
 */
class VideoVoices_Model_User_Table extends Zend_Db_Table_Abstract
{
	/**
	 * Name of the table
	 * @var	string
	 */
	protected $_name = 'users';

	/**
	 * Name of the primary key column
	 * @var	string
	 */
	protected $_primary = 'id';

	//POST controls
	public function getUsersToPostTo(){

		/*
		select  
		u.id, u.fbid, MAX(DATE_ADD( p.`date`, INTERVAL u.`frequency` DAY)) AS nextpost from `users` u 
		LEFT JOIN `posts` p ON u.id = p.user_id 
		WHERE u.`active` = 1 
		AND (DATE_ADD( p.`date`, INTERVAL u.`frequency` DAY) <= CURDATE() OR p.user_id IS NULL)

		GROUP BY u.id 
		ORDER BY nextpost DESC
		*/


		//FREQUENCY IS LISTED INVERSE...

		$table = new self;

		$select = $table->select();
		$select->from( array('u' => 'users'), array(
													'u.id AS id', 
													'u.fbid AS fbid', 
													'u.active AS active',
													'u.frequency AS userfrequency',
													'u.issues_selected AS issues',
													'u.access_token_expiration AS fb_token_expires',
													'u.access_token AS access_token',
													'u.totalfriends AS totalfriends',
													//'DATE_ADD(status_email_sent, INTERVAL 1 MONTH) AS nextStatusEmail'
													//'u.status_email_sent AS status_email_sent'
													)
						);
		
		$select->joinLeft(array('p' => 'posts'), 'u.id = p.user_id' , array(
																		//'p.user_id AS postuserid' , 
																		'MAX(p.date) AS posted_date', //get the MAX posted date
																		'MAX(DATE_ADD( p.date, INTERVAL u.frequency DAY )) AS nextpost'
																		
																		//'DATE_ADD( p.date, INTERVAL u.frequency DAY ) AS nextpost',
																		//"DATE_SUB(CURDATE() , INTERVAL 1 DAY ) AS dayprevious"
																		
																		//'CURDATE() AS currentDate '
																		//'DATE_ADD( p.date, INTERVAL u.frequency DAY) AS nextPostNoMax'
																		
																		));
		
		$select->where( 'u.active=1' );

		//THIS WILL GIVE THE 2nd to most recent POST if a post went out today. 
		//an unlikely scenario but comes up a lot in testing.
		$select->where( 'DATE_ADD( p.date, INTERVAL u.frequency DAY) <= CURDATE() OR p.user_id IS NULL' );

		$select->group( 'u.id' );
		$select->order('nextpost DESC' );

		$select->setIntegrityCheck(false);
		$result = $table->fetchAll($select);
		$usersToPost = $result->toArray();

		return $usersToPost;
	}

	public function getMonthlyUsers(){
		$table = new self;

		$select = $table->select();
		$select->from( array('u' => 'users'), array(
													'u.id AS id', 
													'u.fbid AS fbid', 
													'u.active AS active',
													//'u.frequency AS userfrequency',
													//'u.issues_selected AS issues',
													'u.access_token_expiration AS fb_token_expires',
													'u.access_token AS access_token',
													//'u.totalfriends AS totalfriends',
													//'DATE_ADD(status_email_sent, INTERVAL 1 MONTH) AS nextStatusEmail'
													'u.status_email_sent AS status_email_sent'
													)
						);
		$select->where( 'u.active=1' );
		$select->where( 'DATE_ADD( u.status_email_sent, INTERVAL 1 MONTH ) <= CURDATE() OR u.status_email_sent IS NULL' );

		//$select->setIntegrityCheck(false);
		$result = $table->fetchAll($select);
		$users = $result->toArray();
		return $users;
	}

	//USER Modifications / Edits

	public function getUserCount(){
		$config = Zend_Registry::get('config');
			
		if (APPLICATION_ENV == 'production' || APPLICATION_ENV == 'staging')
		{
			$cache = Zend_Registry::get('cache');
			// see if a cache already exists:
			if( ($result = $cache->load($config->resources->session->name.'totalUserCount')) === false ) 
			{
			 	// cache miss; connect to the database
			 	$result = $this->_fetchUserCount();
			    $cache->save($result, $config->resources->session->name.'totalUserCount');			
			}
			return $result;
		}
		else 
		{
			return $this->_fetchUserCount();
		}
	}

	protected static function _fetchUserCount(){
		
		//I don't know how to do this any other way right now :/
		
		$table = new self;
		//$select = $table->fetchAll();
		//$num = count($select);
		//return $num;
				
		$select = $table->select()
			->from(	array('users'), 
					array('totalusers' => 'count(*)') 
			);
		$result = $table->fetchRow($select);
		return $result->totalusers;
	}

	public function updateAccessToken($fbid, $access_token, $expires){
		$table = new self;
		$select = $table->select()
			->where('fbid=?', $fbid);	
		$userFound = $table->fetchRow($select);
		
		if( $userFound 
			&& isset($fbid)
			&& isset($expires)
			&& isset($access_token)
		){

			$expires_in = $this->getTimeItWillExpire($expires);

			$data = array(	
				'fbid' => $fbid,
				'access_token' => $access_token,
				'access_token_expiration' => $expires_in,
				'active' => 1
			);

			$where = $table->getAdapter()->quoteInto('fbid = ?', $fbid);
			$result = $table->update($data, $where);

			return $result;
		}else{
			return array( 'error' => 'params not defined');
		}
	}

	//pass just FBID to return the user
	//update the user if only certain values are passed
	//add new user if there is no matching FBID in the DB
	public function getUser($userInfo)
	{
		$table = new self;
		$select = $table->select()
			->where('fbid=?', $userInfo['fbid']);	
		$userFound = $table->fetchRow($select);
		
		$justGetUser = true;

		if ($userFound 
				&& isset($userInfo['fbid']) 
				&& isset($userInfo['issues']) 
				&& isset($userInfo['frequency'])
			){
			//UPDATE
			
			$issues = "b'".(string)$userInfo['issues']."'";

			$data = array(	
				'fbid' => $userInfo['fbid'],
				'issues_selected' => new Zend_Db_Expr($issues),
				'frequency' => $userInfo['frequency'],
				'active' => 1
			);

			$where = $table->getAdapter()->quoteInto('fbid = ?', $userInfo['fbid']);
			$table->update($data, $where);
			
			$uid = $userFound->id;
			$users = $table->find($uid);
			$user = $users[0];

		} else if( isset($userInfo['fbid']) 
				&& isset($userInfo['issues']) 
				&& isset($userInfo['frequency'])
				&& isset($userInfo['totalfriends'])  
				){
			
			$issues = "b'".(string)$userInfo['issues']."'";
			$expires_in = $this->getTimeItWillExpire($userInfo['access_token_expiration']);

			//no user so create one...
			
			$uid = $table->insert(array(
				'fbid' => $userInfo['fbid'],
				'issues_selected' => new Zend_Db_Expr($issues), //new Zend_Db_Expr("b'10001'")
				'frequency' => $userInfo['frequency'],
				'totalfriends'=> $userInfo['totalfriends'],
				'access_token' => $userInfo['access_token'],
				'access_token_expiration' => $expires_in,
				'active' => 1,
				'status_email_sent' => date( 'Y-m-d')
			));
			//self::uploadImage('https://graph.facebook.com/' . $facebookUserInfo['id'] . '/picture?type=small', $id);
			
			$users = $table->find($uid);
			$user = $users[0];
		}else if ($userFound ){
			$uid = $userFound->id;
			$users = $table->find($uid);
			$user = $users[0];
			
			$justGetUser = true;

		}else{
			return false;
		}
		
		//ADD leading zeros.
		$user["issues_selected"] = (string) decbin( $user["issues_selected"] );
		if( strlen($user["issues_selected"]) < 5 ){
			$iterate = 5-strlen($user["issues_selected"]);
			for( $k = 0; $k < $iterate; $k++ ){
				$user["issues_selected"] = '0'.$user["issues_selected"];		
			}
		}
		
		
		$userFiltered = array(
			"id" => $user["id"],	
			"fbid" => $user["fbid"],
			"frequency" => $user["frequency"],	
			"issues_selected" => $user["issues_selected"],	
			"totalfriends" => $user["totalfriends"]
			/* ADD USER NAME ? */
		);
		
		if( $justGetUser == true ){
			//these are added values needed for the POST specific user action.
			$userFiltered['access_token'] = $user["access_token"];
			$userFiltered['fb_token_expires'] = $user["access_token_expiration"];
			$userFiltered['nextpost'] = date("Y-m-d");
			$userFiltered['issues'] = $user['issues_selected'];
			$userFiltered['status_email_sent'] = $user['status_email_sent'];
		}

		//do not know what this does:
		//VideoVoices_Model_User_Table::ready($id);
		return $userFiltered;
	}
	
	//update the user's date of when an email was sent.
	public static function updateEmailSentDate($fbid){
		$table = new self;

		$data = array(	
			'status_email_sent' => date( 'Y-m-d')
		);

		$where = $table->getAdapter()->quoteInto('fbid = ?', $fbid );
		$table->update($data, $where);

	}
	

	public function getTimeItWillExpire($expires_in_seconds){
		//60 days from access_token //this time is based off of Pacific Time I reckon.
		return date( 'Y-m-d H:i:s', (time()+$expires_in_seconds) );

	}


	
	

}

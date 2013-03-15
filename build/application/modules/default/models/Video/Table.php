<?php

/**
 * Table for Video
 */
class VideoVoices_Model_Video_Table extends Zend_Db_Table_Abstract
{
	/**
	 * Name of the table
	 * @var	string
	 */
	protected $_name = 'videos';

	/**
	 * Name of the primary key column
	 * @var	string
	 */
	protected $_primary = 'id';
	
	
	/* recent videos list with correspondents */
	public function getRecentVideos()
	{
		$config = Zend_Registry::get('config');
		//echo $config->resources->session->name;
		//die;

		//|| APPLICATION_ENV == 'staging'
		if (APPLICATION_ENV == 'production' )
		{
			$cache = Zend_Registry::get('cache');
			// see if a cache already exists:
			if( ($result = $cache->load($config->resources->session->name.'recentvideos')) === false ) 
			{
			 	// cache miss; connect to the database
			 	$result = $this->_fetchRecentVideosPosted();
			    $cache->save($result, $config->resources->session->name.'recentvideos');			
			}
			return $result;
		}
		else 
		{
			$result = $this->_fetchRecentVideosPosted();
			return $result;
		}
		
		
	}
	
	public function _fetchRecentVideosPosted()
	{	
//		$config = Zend_Registry::get('config');
//		$defaultThumbPath = $config->get('assetUrl');
	
		//get the last 100 posts... 
		//and generate a recent list of videos with user posts for viewing on the front end
		//this list needs users that have contributed by posting

		$table = new self;
		$select = $table->select();
		$select->from( array('u' => 'users'), array('u.id AS contributor_id' , 'u.fbid AS contributor_fbid' ) );
		$select->joinInner(array('p' => 'posts'), 'u.id = p.user_id' , array('p.id AS post_id') );
		$select->joinInner(array('v' => 'videos'), 'v.id = p.video_id', array(
																				'v.id AS video_id', 
																				'v.youtube_id AS video_yt_id', 
																				'v.title AS video_title', 
																				'v.issues AS issues_selected',
																				'DATE(v.posted_date) AS video_date' ));
		
		$select->joinLeft(array('c' => 'correspondent'), 'v.correspondent_id = c.id' , array(
																							'c.fbhandle AS correspondent_fb_handle' , 
																							'c.twitterhandle AS correspondent_twitter_handle' , 
																							'c.name AS correspondent_name' ,
																							'c.address AS correspondent_address'
																							));


		$select->setIntegrityCheck(false);
		$select->where('v.correspondent_id != 0');
		$select->order('v.posted_date DESC, RAND()');
		$select->group('v.id');
		$select->limit(50,0);
			
		//echo $select;
		$result = $table->fetchAll($select);
		$recentPosts = $result->toArray();
		
		//echo ' RECENT POSTS: '. count( $recentPosts );
		//Zend_Debug::dump($recentPosts);
		//echo 'end.<BR><BR>';

		if( count($recentPosts) == 0 ){
			//there are no posts
		}
		

		//filter through post results
		//display by videos (if video already exists do not add to the recentVideos list
		$recentVideos = array();
		foreach($recentPosts as $post ){
			
			$videoHasBeenAdded = false;
			
			//search array being built determine if that video has already been added.
			//if it isn't added create it below (!videoHasBeenAdded){}
			//if it has add the post/correspondent data here to existing entry:
			
			for($rv = 0; $rv < count($recentVideos); $rv++){
				$checkvideo = $recentVideos[$rv];
				//compare added videos to THIS = post video
				if( $checkvideo["id"] == $post["video_id"] ){ 
					//this is not likely to happen in the list of 100 last posts... but pre-cautionary.
					//avoid duplicate user thumbnails in the recentvideo[sharedthumbs] list.
					//have we already added this user to this video shared?
					$fbidAlreadyExists = false;
					foreach( $recentVideos[$rv]["sharedThumbs"] as $fbcontributor ){
						if( $fbcontributor == $post["contributor_fbid"] ){ 
							$fbidAlreadyExists = true;
							break;
						}
					}
					
					//add in another contributor to this video
					//they do not exist in the list of already shared users of this video.
					if(!$fbidAlreadyExists){
						//$recentVideos[$rv]["sharedThumbs"][] = $post["contributor_fbid"];
						$recentVideos[$rv]["sharedThumbs"][] = array("url" => 'http://graph.facebook.com/'.$post["contributor_fbid"].'/picture' );
					}	
					$videoHasBeenAdded = true;
					break; //found the video, added this POSTS contributor to it, break the loop.
				}
			}
			
			//video entry did not exist so create it here and 
			//add all the basic fields needed as well as the first sharedThumb user.
			if(!$videoHasBeenAdded){
				//ADD leading zeros.
				/*
				$issues_selected = $post["issues_selected"];
				$issues_selected = (string) decbin($issues_selected);
				if( strlen($issues_selected) < 5 ){
					$iterate = 5-strlen($issues_selected);
					for( $k = 0; $k < $iterate; $k++ ){
						$issues_selected = '0'.$issues_selected;		
					}
				}
				*/
				
				$issues_selected = $this->convertIssuesToString($post["issues_selected"] );
				
				$vidDate = strtotime( (string)$post["video_date"] );
				$dateFormatted = date('F j, Y', $vidDate );
				
				$correspondent_thumbnail = '';
				if( $post["correspondent_fb_handle"] ){
					$correspondent_thumbnail = 'http://graph.facebook.com/'.$post["correspondent_fb_handle"].'/picture';
				}else if( $post["correspondent_twitter_handle"] ){
					//http://api.twitter.com/1/users/profile_image/:screen_name.format
					//https://dev.twitter.com/docs/api/1/get/users/profile_image/:screen_name
					$correspondent_thumbnail = 'http://api.twitter.com/1/users/profile_image/?screen_name='.$post["correspondent_twitter_handle"].'&size=bigger'; //73x73
				}
				
				//get view count
				$viewcount = 0;
				$yturl = "https://gdata.youtube.com/feeds/api/videos/".$post["video_yt_id"]."?alt=json";
				$ytreturnedobject = json_decode(file_get_contents($yturl), true);			
				
				if( $ytreturnedobject['entry']{'yt$statistics'}['viewCount'] ){
					$viewcount = number_format( $ytreturnedobject['entry']{'yt$statistics'}['viewCount'] );
				}

				$description = '';
				if( $ytreturnedobject["entry"]["content"]{'$t'} ){
					$description = $ytreturnedobject["entry"]["content"]{'$t'};
					
					//CAUTION THIS LOOP TIMES OUT THE SCRIPT, ELLIPSIS IS NOW ADDED ON THE FRONT END.

					//http://www.phpeasystep.com/phptu/19.html
					//$lastCharacterPosition=450; // Define how many characters you want to display.

					// Find what is the last character.
					//$description_lastCharacterPost = substr($description,$lastCharacterPosition,1);

					// In this step, if the last character is not " "(space) run this code.
					// Find until we found that last character is " "(space) 
					// by $position+1 (14+1=15, 15+1=16 until we found " "(space) that mean character no.20) 
					//echo ' $description_lastCharacterPost : ' . $description_lastCharacterPost;
					
					/*
					if($description_lastCharacterPost !=" "){
						while( $description_lastCharacterPost !=" " ){
							$lastCharacterPosition++;
							$description_lastCharacterPost = substr($description,$lastCharacterPosition,1); 
							//echo $lastCharacterPosition . ' - ' . $description_lastCharacterPost;
						}

						$description = substr($description,0,$lastCharacterPosition); 
						$description .= '...';					

					}
					*/
					//$description = substr($description,0,$lastCharacterPosition) . '...'; 

				}
				
				$gotochannelURL = '';
				if( $ytreturnedobject["entry"]["link"][0]['href'] ){
					$gotochannelURL = $ytreturnedobject["entry"]["link"][0]['href'];
				}

				$video = array(
							"id"=>$post["video_id"],
							"viewcount"=>$viewcount,
							"description"=>$description,
							"channellink"=>$gotochannelURL,
							"title"=>$post["video_title"],
							"ytid"=> $post["video_yt_id"],
							"issues"=> $issues_selected,
							"issues_selected"=> $post["issues_selected"],
							"date"=> $dateFormatted,
							"date_default"=>$post["video_date"],
							"correspondent" => array(
											"name"=>$post["correspondent_name"],
											"fb"=>$post["correspondent_fb_handle"],
											"twitter"=>$post["correspondent_twitter_handle"],
											"location"=>$post["correspondent_address"],
											"thumb"=>$correspondent_thumbnail
											),
							"sharedThumbs" => array()
						);	
									
				$video["sharedThumbs"][]= array("url" => 'http://graph.facebook.com/'.$post["contributor_fbid"].'/picture' );
				$recentVideos[] = $video;
			}
		}
		
		//echo '<br><br><br>';
		//var_dump($recentVideos);
		//die;
		
		return $recentVideos;
		
		/*
		SELECT v.*, c.*, u.*
		FROM users u 
		INNER JOIN posts p ON u.id = p.user_id
		INNER JOIN videos v ON v.id = p.video_id
		LEFT JOIN correspondent c ON v.`correspondent_id` = c.id
		ORDER BY v.posted_date DESC, RAND()
		LIMIT 100
		*/
	}

	public function getLastVideosAdded(){

		//different from above as it only looks for recent videos added to the DB
		//doesn't matter if they have been posted
		$table = new self;
		$select = $table->select();
		$select->from( array('v' => 'videos'), array(
															'v.id AS video_id', 
															'v.youtube_id AS video_yt_id', 
															'v.title AS video_title', 
															'v.issues AS issues_selected',
															'DATE(v.posted_date) AS video_date' ));
			
		$select->joinLeft(array('c' => 'correspondent'), 'c.id = v.correspondent_id' , array(
															'c.fbhandle AS correspondent_fb_handle' , 
															'c.twitterhandle AS correspondent_twitter_handle' , 
															'c.name AS correspondent_name' ,
															'c.address AS correspondent_address'
															));

		$select->setIntegrityCheck(false);
		$select->where('v.correspondent_id != 0');
		$select->order('v.posted_date DESC, RAND()');
		$select->limit(100,0);
			
		//echo $select;
		$result = $table->fetchAll($select);
		$recentlyAddedVideos = $result->toArray();
		
		//echo ' RECENT POSTS: '. count( $recentlyAddedVideos );
		
		
		//filter through post results
		//display by videos (if video already exists do not add to the recentVideos list
		$recentVideos = array();
		foreach($recentlyAddedVideos as $post ){
			
			//ADD leading zeros.
			//$issues_selected = $this->convertIssuesToString($post["issues_selected"] );
			
			$vidDate = strtotime( (string)$post["video_date"] );
			$dateFormatted = date('F j, Y', $vidDate );
			
			/*
			$correspondent_thumbnail = '';
			if( $post["correspondent_fb_handle"] ){
				$correspondent_thumbnail = 'http://graph.facebook.com/'.$post["correspondent_fb_handle"].'/picture';
			}else if( $post["correspondent_twitter_handle"] ){
				//http://api.twitter.com/1/users/profile_image/:screen_name.format
				//https://dev.twitter.com/docs/api/1/get/users/profile_image/:screen_name
				$correspondent_thumbnail = 'http://api.twitter.com/1/users/profile_image/?screen_name='.$post["correspondent_twitter_handle"].'&size=bigger'; //73x73
			}
			*/

			$video = array(
						"id"=>$post["video_id"],
						"title"=>$post["video_title"],
						"ytid"=> $post["video_yt_id"],
						//"issues"=> $issues_selected,
						"issues_selected"=> $post["issues_selected"],
						"date"=> $dateFormatted,
						"date_default"=>$post["video_date"],

						//do we need this:
						"correspondent" => array(
										"name"=>$post["correspondent_name"]
										/*,
										"fb"=>$post["correspondent_fb_handle"],
										"twitter"=>$post["correspondent_twitter_handle"],
										"location"=>$post["correspondent_address"],
										"thumb"=>$correspondent_thumbnail
										*/
										)
					);	
								
			$recentVideos[] = $video;
		}
		
		//echo '<br><br><br>';
		//Zend_Debug::dump($recentVideos);
		//echo '<br><br><br>';
		
		return $recentVideos;
		
	}



// ---------- CMS ADMIN functions
	
	public function getVideos($startAt=1 , $perPage){
		$table = new self;
		$select = $table->select();
		$select->order('id DESC');
		$select->limitPage($startAt, $perPage);
		
		$result = $table->fetchAll($select);
		$resultVideos = $result->toArray();
		
		$returnValue = array();
		
		if( count($resultVideos) > 0 ){
			foreach($resultVideos as $videoItem ){
				/*
				$issues_selected = $videoItem["issues"];
				$issues_selected = (string) decbin($issues_selected);
				if( strlen($issues_selected) < 5 ){
					$iterate = 5-strlen($issues_selected);
					for( $k = 0; $k < $iterate; $k++ ){
						$issues_selected = '0'.$issues_selected;		
					}
				}*/
				$videoItem["issues"] = $this->convertIssuesToString($videoItem["issues"]);//$issues_selected;
				$returnValue[] = $videoItem;
			}
		}
		
		return $returnValue;
	}

	public function addNewVideo($data){
		$table = new self;


		//search for a video that already exists here with that matching yid.
		$select = $table->select()
				->where('youtube_id = "'.$data["youtube_id"].'" ' );
		$videoFound = $table->fetchRow($select);

		if( $videoFound ){

			return false; 
		}else{
			$data['issues'] = $this->convertIssuesToBit($data['issues'] );
			$table->insert($data);
			//NEED TO ADDRESS A WAY TO GET CALLBACK FROM THESE:	
			return true;
		}
		
		
	}
	
	//NEED TO ADDRESS A WAY TO GET CALLBACK FROM THESE:
	public function updateVideo($id , $data){
			$table = new self;
			
			$data['issues'] = $this->convertIssuesToBit($data['issues']);
			
			$where = $table->getAdapter()->quoteInto('id = ?', $id);
			$table->update($data, $where);
			
			return true;//$returnValue;
	}
	
	//NEED TO ADDRESS A WAY TO GET CALLBACK FROM THESE:
	public function deleteVideo($id ){
		$table = new self;
		
		$where = $table->getAdapter()->quoteInto('id = ?',$id);
		$table->delete($where);
		
		//$result = $table->fetchAll();
		//$returnValue = $result->toArray();
		
		return true;//$returnValue;
	}

	public function convertIssuesToBit($_issues){
		return new Zend_Db_Expr( "b'".(string)$_issues."'" );
	}
	
	public function convertIssuesToString($issues_selected){
		$issues_selected = (string) decbin($issues_selected);
		if( strlen($issues_selected) < 5 ){
			$iterate = 5-strlen($issues_selected);
			for( $k = 0; $k < $iterate; $k++ ){
				$issues_selected = '0'.$issues_selected;		
			}
		}
		return $issues_selected;
	}





/**
	* Returns a list of all videos published since the given date
	*/
	/*
	public function getSince($date)
	{
		// get all videos
		$videos = $this->fetchRecentVideos();
		
		// return objects of type VideoVoices_Model_Video
		return $videos; 
	}
	*/
	/**
	* Returns a list of all videos that might need to be syndicated
	*/
	/*
	public function fetchRecentVideos()
	{
		$config = Zend_Registry::get('config');
	
		if (APPLICATION_ENV == 'production' || APPLICATION_ENV == 'staging')
		{
			$cache = Zend_Registry::get('cache');
	 
			// see if a cache already exists:
			if( ($result = $cache->load('allVideos')) === false ) 
			{
			 	// cache miss; connect to the database
			 	$result = $this->_fetchRecentVideos();
			 
			    $cache->save($result, 'allVideos');			
			}

			return $result;
		}
		else 
		{
			return $this->_fetchRecentVideos();
		}
	}
	
	private function _fetchRecentVideos()
	{
		$daysago = date('Y-m-d', strtotime('today - ' . $config->frequency->minimum . ' days'));
		$table = new self;
		$result = $table->fetchAll($table->select()->where('posted_timestamp > ?', $daysago));
		
		$videos = array();
		foreach ($result as $row)
		{
			$video = new VideoVoices_Model_Video($row->toArray());
			$videos[] = $video;
		}
		
		return $videos;
	}
	*/



}

<?php


/**
 * Table for Users
 */
class VideoVoices_Model_Posts_Table extends Zend_Db_Table_Abstract
{
	/**
	 * Name of the table
	 * @var	string
	 */
	protected $_name = 'posts';

	/**
	 * Name of the primary key column
	 * @var	string
	 */
	protected $_primary = 'id';
	
	
	
	public function getTotalRaised(){
		$config = Zend_Registry::get('config');
			
		if (APPLICATION_ENV == 'production' || APPLICATION_ENV == 'staging')
		{
			$cache = Zend_Registry::get('cache');
			// see if a cache already exists:
			if( ($result = $cache->load($config->resources->session->name.'totalImpressionsRaised')) === false ) 
			{
			 	// cache miss; connect to the database
			 	$result = $this->_fetchTotalRaised();
			    $cache->save($result, $config->resources->session->name.'totalImpressionsRaised');			
			}
			return $result;
		}
		else 
		{
			return $this->_fetchTotalRaised();
		}
	}

	protected function _fetchTotalRaised(){
		$table = new self;
		$select = $table->select()
			->from(array('posts'), 
				array('friendssum' => 'sum(friends)',
					'postsum' => 'count(*)')
				);
				
		$result = $table->fetchRow($select);
		//$result->postsum isn't needed.
		//don't want to factor the friends, this is the total friends of all the posts already factored:
		$moneyRaised = $this->impressionsToMoneyRaised(1 , $result->friendssum);
		
		//echo $result->postsum . ' ---- ' . $result->friendssum . ' --- ' ;
		//echo $moneyRaised;
		//die;

		return $moneyRaised;
	}
	
	public function getTotalsForUser($user_id){
		$table = new self;
		
		$selectImpressions = $table->select()
						->from(array('posts'),
							array('friendssum'=>'sum(friends)' ,
								'postsum'=> 'count(*)') )
						->where('user_id = '. $user_id );

		$result = $table->fetchRow($selectImpressions);
		$moneyRaised = $this->impressionsToMoneyRaised($result->postsum , $result->friendssum );
		$impressions = $this->impressionsMade($result->postsum , $result->friendssum);		

		//echo $result->postsum .' ---- '. $result->friendssum;

		return array('totalposts'=> $result->postsum , 'moneyraised'=>$moneyRaised , 'impressions'=>$impressions );

	}

	public function findMatchingPost($user_id , $video_id){
		$table = new self;

		$select = $table->select()
					->where('user_id = '. $user_id )
					->where('video_id = '. $video_id );

		
		$postFound = $table->fetchRow($select);
		return $postFound;
	}

	public function findMatchingPosts($user_id , $videos){
		$table = new self;
		
		$select = $table->select()
					->from(array('posts'), 
							array('video_id' ) 
							) 
					->where('user_id = '. $user_id )
					->where('video_id IN (?)', $videos ); //http://framework.zend.com/manual/en/zend.db.select.html

		//$table->setFetchMode(Zend_Db::FETCH_NUM);
		//fetchCol();

		$result = $table->fetchAll($select);
		$postsFound = $result->toArray();
		$returnIDS = array();
		foreach ($postsFound as $post) { $returnIDS[] = $post['video_id']; }
		
		//echo ' <BR><BR> posts found: ';
		//var_dump($returnIDS);
		return $returnIDS;
	}
	

	public function addPost($post){
		$table = new self;

		$uid = $table->insert(array(
				'user_id' => $post['uid'],
				'video_id' => $post['vid'],
				'date' => $post['date'],
				'friends'=> $post['totalfriends']
			));

		return $uid;
	}

	public function impressionsMade($posts, $friends){
		$impressions = ( ( $friends * $posts) * 0.12 );
		//make it a full number. can be up to 216,000
		return round($impressions);
	}

	public function impressionsToMoneyRaised($posts , $friends){
		// number of friends(potential viewers) * number of posts * Impressions Per Post * Value Per Impression
		//could be as high as 21,600
		//60 * 34461 * .12 * .01 = 
		$moneyraised = ((($friends*$posts)*0.12)*0.01);

		return round($moneyraised, 2 );
	}
	
	
}

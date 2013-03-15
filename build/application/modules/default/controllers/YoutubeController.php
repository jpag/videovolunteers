<?php

class YoutubeController extends VideoVoices_Controller_FrontAction
{
	/**
	 * Static config from application.ini
	 * @var Zend_Config
	 */
	protected $config;
	
	protected $youtube;

	public function init()
	{
		error_reporting(E_ALL);
		parent::init();

		//$this->config = Zend_Registry::get('config');
		$this->config = Zend_Registry::get('config');
		
		$this->_helper->layout->setLayout('cron');			          
		
		$this->_helper->contextSwitch()
			             ->addActionContext('index', 'json');
		

	}

	public function indexAction()
	{
		//pull youtube feed and scrape latest videos. auto pull them in. (default to what issues?)
		//$yturl =  'http://gdata.youtube.com/feeds/api/users/'.$this->config->youtube->channel.'/playlists?alt=json';
		$this->view->video = array();

		$yturl = $this->config->httpprotocol . '://gdata.youtube.com/feeds/base/users/';
		$yturl .= $this->config->youtube->channel;
		$yturl .= '/uploads?v=2&orderby=published&client=ytapi-youtube-profile&alt=json';

		//echo $yturl . '<br><br>';

        $result = json_decode(file_get_contents($yturl), true);
        $entries = $result["feed"]["entry"];

		//Zend_Debug::dump( $entries );


		foreach( $entries as $entry ){
			$entryurl = $entry["link"][0]["href"];
			//echo $entryurl . ' ';

			parse_str( parse_url( $entryurl, PHP_URL_QUERY ), $my_array_of_vars );
			//echo $my_array_of_vars['v'] . '<br>'; 

			echo $entry["published"]{'$t'};
			//NEED TO SET A DEFAULT CORRESPONDENT..
			$video = array(
				'title' => $entry["title"]{'$t'},
				'youtube_id' => $my_array_of_vars['v'],
				'correspondent_id' => 0,
				'posted_date' => $entry["published"]{'$t'},
				'issues' => '00000'
			);

			//Zend_Debug::dump($video);
			//check to see if this item exists in the DB.
			$videos = new VideoVoices_Model_Video_Table();
			$success = $videos->addNewVideo( $video );
			if( $success ){
				array_push($this->view->video, $video);
				echo '<br> added :';
				Zend_Debug::dump($video);
				echo '<br>';
			}else{
				echo 'write to DB failed. probably already exists<br>';
			}

		}

		//Zend_Debug::dump($this->view->video);
	}

}


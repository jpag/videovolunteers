<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/** 
	 * Initialise our config
	 * @return Zend_Config 
	 */
	protected function _initConfig()
	{   
		$config = new Zend_Config($this->getOptions(), true);
		Zend_Registry::set('config', $config);
		

		if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])){
			$httpprotocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
		}else{
			$httpprotocol = !empty($_SERVER['HTTPS']) ? "https" : "http";
		}

		$config->httpprotocol = $httpprotocol;
		

		if (APPLICATION_ENV == 'production' || APPLICATION_ENV == 'staging') {//} || APPLICATION_ENV == 'test') {
			$assetsIni = parse_ini_file(__DIR__ . '/../../configs/assets.ini');
			$assetRevision = $assetsIni['revision'];
			if( $assetRevision > 0 ){
				$config->assetUrl .= $assetRevision . '/';
			}
		} else if (isset($_SERVER['HTTP_HOST'])) {
			$config->assetUrl = $config->httpprotocol.'://' . $_SERVER['HTTP_HOST'] . '/assets/';
		}
		return $config;
	}

	public function _initLog()
	{
		if (APPLICATION_ENV != 'production') 
		{
			$writer = new Zend_Log_Writer_Firebug();
			$logger = new Zend_Log($writer);
			
			Zend_Registry::set('log', $logger);
		}
	}

	public function _initCache()
	{
		if (APPLICATION_ENV == 'production' || APPLICATION_ENV == 'staging')
		{
			$frontendOptions = array(
			   'lifetime' => Zend_Registry::get('config')->get('settingsCacheTime'),
			   'automatic_serialization' => true
			);
			
			if (APPLICATION_ENV == 'production' || APPLICATION_ENV == 'staging' ){
				$backendOptions = array(
				    'cache_dir' => __DIR__.'/../../../../../data/tmp/' // Directory where to put the cache files
				);
			} else{
				$backendOptions = array(
				    'cache_dir' => '/dev/shm/' // Directory where to put the cache files
				);
			}

			// getting a Zend_Cache_Core object
			$cache = Zend_Cache::factory('Core',
			                             'File',
			                             $frontendOptions,
			                             $backendOptions);
			
			Zend_Registry::set('cache', $cache);
		}
	}
	
	public function _initLayout()
	{
		Zend_Layout::startMvc();

		$layout = Zend_Layout::getMvcInstance();
		$view = $layout->getView();
		$view->addHelperPath('/views/helpers', 'VideoVoices_View_Helper');
	}
	
	public function _initRoutes()
	{
		if (!isset($_SERVER['REQUEST_URI'])) {
			return;
		}

		$path = $_SERVER['REQUEST_URI'];
		$prefix = '/';

		$this->bootstrap('FrontController')->bootstrap('Router')->bootstrap('db');

		$front =  $this->getResource('FrontController');
		$router = $front->getRouter();

		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
		

		//index 
		$router->addRoute('general', new Zend_Controller_Router_Route(
			'/:action',
			array(
				'module' => 'default',
				'controller' => 'index',
				'action' => 'index',
			)
		));
		
		//connect with facebook auth
		//update access token (if it expires won't be able to post)
		$router->addRoute('facebook', new Zend_Controller_Router_Route(
			'/facebook/:action/:format',
			array(
				'module' => 'default',
				'controller' => 'facebook',
				'action' => 'index',
				'format'=>''
			)
		));

		//cms
			//video add/edit
			//correspondents add/edit
		$router->addRoute('admin', new Zend_Controller_Router_Route(
			'/admin/:action/:page/:format',
			array(
				'module' => 'default',
				'controller' => 'admin',
				'action' => 'videos',
				'format' => '',
				'page' => '1'
			)
		));
		
		//auto post to wall script (all or specific FBID)
		$router->addRoute('post', new Zend_Controller_Router_Route(
			'/post/:fbid',
			array(
				'module' => 'default',
				'controller' => 'post',
				'action' => 'index',
				'fbid' => ''
			)
		));
		
		//monthly email sent
		$router->addRoute('monthly', new Zend_Controller_Router_Route(
			'/monthly/:fbid',
			array(
				'module' => 'default',
				'controller' => 'monthly',
				'action' => 'index',
				'fbid' => ''
			)
		));
		

		//youtube pull latest videos script
		$router->addRoute('youtube', new Zend_Controller_Router_Route(
			'/youtube/:action',
			array(
				'module' => 'default',
				'controller' => 'youtube',
				'action' => 'index',
			)
		));

		// on user updating their account: userupdate post 
		// on start recentvideos list
		// uid is used for userstat/uid
		$router->addRoute('requests', new Zend_Controller_Router_Route(
			'/requests/:action/:uid',
			array(
				'module' => 'default',
				'controller' => 'requests',
				'action' => 'index',
				'format' => 'json',
				'uid' => ''
			)
		));



	}
}

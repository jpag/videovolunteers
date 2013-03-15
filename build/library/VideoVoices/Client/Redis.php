<?php

class VideoVoices_Client_Redis
{
	protected $predisClient;

	public function __construct()
	{
		require_once(__DIR__ . '/../../Predis/Autoloader.php');
		Predis\Autoloader::register();

		$config = Zend_Registry::get('config');
		$this->predisClient = new Predis\Client(array(
			'host' => $config->get('redisHost'),
			'port' => $config->get('redisPort'),
			'password' => $config->get('redisPassword'),
		));
	}

	public function publish($message)
	{
		$this->predisClient->publish(Zend_Registry::get('config')->get('redisChannel'), $message);
	}
}

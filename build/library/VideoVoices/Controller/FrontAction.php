<?php

class VideoVoices_Controller_FrontAction extends Zend_Controller_Action
{
	const SECRET_TOKEN = 'LJlSiGdSbDLcC21y7s7hzw';

	public function init()
	{
		$this->view->setEncoding('utf-8');

		$config = Zend_Registry::get('config');
		$this->view->assetUrl = $config->get('assetUrl');
	}
}

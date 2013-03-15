<?php

class VideoVoices_Client_Email
{
	public static function register($id, $email)
	{
		$data = array(
			'user_id' => $id,
			'user_email' => $email,
		);
		return self::call('Register', $data);
	}

	public static function send($userId, $messageId)
	{
		$data = array(
			'user_id' => $userId,
			'message_id' => $messageId,
		);
		return self::call('Send', $data);
	}

	protected static function call($op, $data)
	{
		$config = Zend_Registry::get('config');
		$data += array(
			'api_key' => $config->get('emailApiKey'),
		);
		$context = stream_context_create(array('http' => array(
			'method' => 'POST',
			'content' => http_build_query($data),
		)));
		file_get_contents($config->get('emailApiEndpoint' . $op), false, $context);
	}
}

<?php

class VideoVoices_Validate_EmailAddress extends Zend_Validate_EmailAddress
{
	
	const MESSAGE = 'Invalid email address, please try again.';
	
    public function getMessages()
    {
    	$messages = parent::getMessages();
    	if (count($messages) > 0) {
    		return array(
    			self::INVALID => self::MESSAGE,
    		);
    	}
		return $messages;
    }
}

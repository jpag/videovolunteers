<?php

class VideoVoices_Form extends Zend_Form
{
	public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
        
    	$this->addPrefixPath('VideoVoices_Form_Element', 'VideoVoices/Form/Element/', 'element');

        // Extensions...
        $this->init();

        $this->loadDefaultDecorators();
    }

}
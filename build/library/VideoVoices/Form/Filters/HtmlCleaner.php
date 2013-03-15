<?php

class VideoVoices_Form_Filters_HtmlCleaner implements Zend_Filter_Interface
{
    protected $htmlPurifier;
   
    public function __construct()
    {
        require_once 'HTMLPurifier/HTMLPurifier.auto.php';
        
        $config = HTMLPurifier_Config::createDefault();
        
        $config->set('HTML.Allowed', 'p,em,strong,a[href],br');
        $config->set('AutoFormat.Linkify', 'false');
        $config->set('AutoFormat.AutoParagraph', 'true');            
		$config->set('Cache.DefinitionImpl', null);
        
        $this->htmlPurifier = new HTMLPurifier($config);
    }

    public function filter($value)
    {
        return $this->htmlPurifier->purify($value);
    }
}

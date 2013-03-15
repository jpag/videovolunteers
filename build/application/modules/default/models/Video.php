<?php


/*
class VideoVoices_Model_Video
{
	protected $_id;
	protected $_data;
	
	public function __get($name)
	{
		if (isset($this->_data[$name]))
			return $this->_data[$name]; 
			
		if ($name == 'id')
			return $this->_id;
	}
	
	public function __set($name, $value)
	{
		if ($name == '')
			return;
			
		$this->_data[$name] = $value;
		
		if ($name == 'id')
			$this->_id = $value;
	}
	
	public function __construct($properties = null)
	{
		if (is_null($properties))
			return $this;
			
		if (is_array($properties))
		{
			foreach ( $properties as $var => $property )
			{
				$this->$var = $property;
			}
		}
	}
*/	
	
	/**
	 * Returns a boolean based on the given issues of interest, true if there is any intersection with this video's isses, false otherwise
	 * NB. Video must already be initialised
	 * @var	string
	 */
/*
	public function getInterest($bitstring)
	{
		// get bitstring of this video
		if (!isset($this->issues))
			return false;
			
		if ( $this->issues & $bitstring > 0 )
			return true;
			
		return false;
	}
}
*/
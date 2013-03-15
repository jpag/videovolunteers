<?php

class VideoVoices_Validate_ImageFile extends Zend_Validate_Abstract
{
	protected $dims;
	protected $isMain;

	const SIZE = 'size';
	const SIZE_UNSTRICT = 'unstrict';
	const INVALID = 'invalid';

	protected $providedDimsLabel;
	protected $dimsLabel;

	protected $_messageVariables = array(
		'dims' => 'dimsLabel',
		'providedDims' => 'providedDimsLabel',
	);

	protected $_messageTemplates = array(
		self::INVALID => 'Cannot read image.',
		self::SIZE => 'Invalid image size %providedDims%, must be %dims%.',
		self::SIZE_UNSTRICT => 'Invalid image size %providedDims%, must be at least %dims%.'
	);

	public function __construct($dims, $isMain = false)
	{
		$this->dims = $dims;
		$this->isMain = $isMain;
		$this->dimsLabel = VideoVoices_Model_ImageType_Row::getDimensionsLabel($dims);
	}

	/**
	 * @param	$value	Path to the temporary file
	 */
	public function isValid($value, $context = null)
	{
		// if we're cropping, only require that the upload is >= the required dims,
		// and only require the main image
		$isStrict = empty($_POST['crop']);

		if (!$value && !$isStrict) {
			return true;
		}

		if (!is_readable($value)) {
			$this->_error(self::INVALID);
			return false;
		}

		$dims = getimagesize($value);
		if (!$dims) {
			$this->_error(self::INVALID);
			return false;
		}

		list($width, $height) = $dims;
		$this->providedDimsLabel = $width . 'x' . $height;

		foreach ($this->dims as $dim) {
			list($dimW, $dimH) = $dim;
			if (($width == $dimW || !$isStrict && $width > $dimW) && ($height == $dimH || !$isStrict && $height > $dimH)) {
				return true;
			}
		}

		$this->_error($isStrict ? self::SIZE : self::SIZE_UNSTRICT);
		return false;
	}
}

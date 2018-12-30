<?php
namespace DataLinx\ImgServer;

use Exception;
use Picqer\Barcode\BarcodeGenerator;

/**
 * Barcode helper class
 */
class BarcodeHelper
{
	/* Content formats */
	const FORMAT_SVG = 'svg';
	const FORMAT_PNG = 'png';
	const FORMAT_JPG = 'jpg';
	const FORMAT_HTML = 'html';

	/* Copied from BarcodeGenerator class */
	const TYPE_CODE_39 = 'C39';
	const TYPE_CODE_39_CHECKSUM = 'C39+';
	const TYPE_CODE_39E = 'C39E';
	const TYPE_CODE_39E_CHECKSUM = 'C39E+';
	const TYPE_CODE_93 = 'C93';
	const TYPE_STANDARD_2_5 = 'S25';
	const TYPE_STANDARD_2_5_CHECKSUM = 'S25+';
	const TYPE_INTERLEAVED_2_5 = 'I25';
	const TYPE_INTERLEAVED_2_5_CHECKSUM = 'I25+';
	const TYPE_CODE_128 = 'C128';
	const TYPE_CODE_128_A = 'C128A';
	const TYPE_CODE_128_B = 'C128B';
	const TYPE_CODE_128_C = 'C128C';
	const TYPE_EAN_2 = 'EAN2';
	const TYPE_EAN_5 = 'EAN5';
	const TYPE_EAN_8 = 'EAN8';
	const TYPE_EAN_13 = 'EAN13';
	const TYPE_UPC_A = 'UPCA';
	const TYPE_UPC_E = 'UPCE';
	const TYPE_MSI = 'MSI';
	const TYPE_MSI_CHECKSUM = 'MSI+';
	const TYPE_POSTNET = 'POSTNET';
	const TYPE_PLANET = 'PLANET';
	const TYPE_RMS4CC = 'RMS4CC';
	const TYPE_KIX = 'KIX';
	const TYPE_IMB = 'IMB';
	const TYPE_CODABAR = 'CODABAR';
	const TYPE_CODE_11 = 'CODE11';
	const TYPE_PHARMA_CODE = 'PHARMA';
	const TYPE_PHARMA_CODE_TWO_TRACKS = 'PHARMA2T';

	/* Defaults */
	const DEFAULT_FORMAT = self::FORMAT_SVG;
	const DEFAULT_TYPE = self::TYPE_EAN_13;
	const DEFAULT_WIDTH_FACTOR = 2;
	const DEFAULT_HEIGHT = 30;
	const DEFAULT_COLOR = 'black';
	const DEFAULT_COLOR_RGB = [0, 0, 0];

	/**
	 * Type of the code we want to generate.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Width factor.
	 *
	 * @var int
	 */
	protected $widthFactor;

	/**
	 * Total code height in px.
	 *
	 * @var int
	 */
	protected $height;

	/**
	 * Foreground color.
	 *
	 * @var array|string
	 */
	protected $color;

	/**
	 * Content format
	 *
	 * @var string
	 */
	protected $format;

	/**
	 * Internal BarcodeGenerator instances
	 *
	 * @var BarcodeGenerator[]
	 */
	private static $_generators;

	/**
	 * Create a new helper instance
	 *
	 * @param string $format Content format (default: SVG. See class constants)
	 * @param string $type Code type (default: EAN 13. See class constants)
	 * @param int $widthFactor Width factor (default: 2)
	 * @param int $height Height in px (default: 30)
	 * @param string|array $color Color (string for HTML and SVG, RGB array for PNG and JPG)
	 */
	public function __construct($format = self::DEFAULT_FORMAT, $type = self::DEFAULT_TYPE, $widthFactor = self::DEFAULT_WIDTH_FACTOR, $height = self::DEFAULT_HEIGHT, $color = NULL)
	{
		$this->format = $format;
		$this->type = $type;
		$this->widthFactor = (int) $widthFactor;
		$this->height = (int) $height;
		$this->color = $color;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get width factor
	 *
	 * @return int
	 */
	public function getWidthFactor()
	{
		return $this->widthFactor;
	}

	/**
	 * Get height in px
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Get color
	 *
	 * @return string|array
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * Get content format
	 *
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * Set type
	 *
	 * @param string $type Code type
	 * @return $this
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * Set width factor
	 *
	 * @param int $widthFactor Width factor
	 * @return $this
	 */
	public function setWidthFactor($widthFactor)
	{
		$this->widthFactor = (int) $widthFactor;
		return $this;
	}

	/**
	 * Set height
	 *
	 * @param int $height Height in px
	 * @return $this
	 */
	public function setHeight($height)
	{
		$this->height = (int) $height;
		return $this;
	}

	/**
	 * Set color (string for HTML/SVG, RGB array for PNG/JPG)
	 *
	 * @param string|array $color Color
	 * @return $this
	 */
	public function setColor($color)
	{
		$this->color = $color;
		return $this;
	}

	/**
	 * Set content format
	 *
	 * @param string $format Content format
	 * @return $this
	 */
	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}

	/**
	 * Get raw library output
	 *
	 * @param string $code Code
	 * @return string
	 */
	public function getContent($code)
	{
		return self::_raw($this->format, $code, $this->type, $this->widthFactor, $this->height, $this->color);
	}

	/**
	 * Get embeddable content, e.g. for img src attribute
	 *
	 * @param string $code Code
	 * @return string
	 * @throws Exception
	 */
	public function getBarcode($code)
	{
		switch ($this->format) {
			case self::FORMAT_JPG:
			case self::FORMAT_PNG:
				return 'data:image/' . $this->format . ';base64,' . base64_encode($this->getContent($code));
			case self::FORMAT_SVG:
				return 'data:image/svg+xml;base64,' . base64_encode($this->getContent($code));
			case self::FORMAT_HTML:
				return $this->getContent($code);
			default:
				throw new Exception('Unknown barcode format!');
		}
	}

	/**
	 * Get the 'src' content for embedding in a SVG 'img' element.
	 *
	 * @param string $code Code
	 * @param string $type Barcode type (see class constants)
	 * @param int $widthFactor Width factor
	 * @param int $height Height
	 * @param string Color
	 * @return string
	 */
	public static function embedSVG($code, $type = self::DEFAULT_TYPE, $widthFactor = self::DEFAULT_WIDTH_FACTOR, $height = self::DEFAULT_HEIGHT, $color = NULL)
	{
		return 'data:image/svg+xml;base64,' . base64_encode(self::_raw(self::FORMAT_SVG, $code, $type, $widthFactor, $height, $color));
	}

	/**
	 * Get the 'src' content for embedding in a PNG 'img' element.
	 *
	 * @param string $code Code
	 * @param string $type Barcode type (see class constants)
	 * @param int $widthFactor Width factor
	 * @param int $height Height
	 * @param array Color (RGB array)
	 * @return string
	 */
	public static function embedPNG($code, $type = self::DEFAULT_TYPE, $widthFactor = self::DEFAULT_WIDTH_FACTOR, $height = self::DEFAULT_HEIGHT, $color = NULL)
	{
		return 'data:image/png;base64,' . base64_encode(self::_raw(self::FORMAT_PNG, $code, $type, $widthFactor, $height, $color));
	}

	/**
	 * Get the 'src' content for embedding in a JPG 'img' element.
	 *
	 * @param string $code Code
	 * @param string $type Barcode type (see class constants)
	 * @param int $widthFactor Width factor
	 * @param int $height Height
	 * @param array Color (RGB array)
	 * @return string
	 */
	public static function embedJPG($code, $type = self::DEFAULT_TYPE, $widthFactor = self::DEFAULT_WIDTH_FACTOR, $height = self::DEFAULT_HEIGHT, $color = NULL)
	{
		return 'data:image/jpg;base64,' . base64_encode(self::_raw(self::FORMAT_JPG, $code, $type, $widthFactor, $height, $color));
	}

	/**
	 * Get the inline code to show the barcode in HTML.
	 *
	 * @param string $code Code
	 * @param string $type Barcode type (see class constants)
	 * @param int $widthFactor Width factor
	 * @param int $height Height
	 * @param string Color
	 * @return string
	 */
	public static function html($code, $type = self::DEFAULT_TYPE, $widthFactor = self::DEFAULT_WIDTH_FACTOR, $height = self::DEFAULT_HEIGHT, $color = NULL)
	{
		return self::_raw(self::FORMAT_HTML, $code, $type, $widthFactor, $height, $color);
	}

	/**
	 * Get raw Barcode library output
	 *
	 * @param string $format Output format (see class FORMAT_ constants for available formats)
	 * @param string $code Code
	 * @param string $type Barcode type (see class constants)
	 * @param int $widthFactor Width factor
	 * @param int $height Height
	 * @param string|array Color
	 * @return string
	 */
	private static function _raw($format, $code, $type, $widthFactor, $height, $color)
	{
		if (empty($color)) {
			switch ($format) {
				case self::FORMAT_HTML:
				case self::FORMAT_SVG:
					$color = self::DEFAULT_COLOR;
					break;

				default:
					$color = self::DEFAULT_COLOR_RGB;
					break;
			}
		}

		return self::_getGenerator($format)->getBarcode($code, $type, $widthFactor, $height, $color);
	}

	/**
	 * Get Barcode generator instance for the specified format.
	 *
	 * @param string $format Content format
	 * @return BarcodeGenerator
	 * @throws Exception
	 */
	private static function _getGenerator($format)
	{
		if (empty($format)) {
			throw new Exception('Barcode format is required');
		}

		if (!isset(self::$_generators[$format])) {
			switch ($format) {
				case self::FORMAT_SVG:
				case self::FORMAT_PNG:
				case self::FORMAT_JPG:
				case self::FORMAT_HTML:
					$class = '\Picqer\Barcode\BarcodeGenerator' . strtoupper($format);
					self::$_generators[$format] = new $class;
					break;
				default:
					throw new Exception("Barcode format {$format} is unknown!");
			}
		}

		return self::$_generators[$format];
	}
}

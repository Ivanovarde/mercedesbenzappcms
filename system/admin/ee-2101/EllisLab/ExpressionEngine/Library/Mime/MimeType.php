<?php

namespace EllisLab\ExpressionEngine\Library\Mime;

use Exception;
use InvalidArgumentException;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.10.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Mime Class
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Core
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class MimeType {

	protected $whitelist = array();
	protected $images    = array();

	/**
	 * Constructor
	 *
	 * @param array $mimes An array of MIME types to add to the whitelist
	 * @return void
	 */
	public function __construct(array $mimes = array())
	{
		$this->addMimeTypes($mimes);
	}

	/**
	 * Adds a mime type to the whitelist
	 *
	 * @param string $mime The mime type to add to the whitelist
	 * @return void
	 */
	public function addMimeType($mime)
	{
		if ( ! is_string($mime))
		{
			throw new InvalidArgumentException("addMimeType only accepts strings; " . gettype($mime) . " found instead.");
		}

		if (count(explode('/', $mime)) != 2)
		{
			throw new InvalidArgumentException($mime . " is not a valid MIME type.");
		}

		if ( ! in_array($mime, $this->whitelist))
		{
			$this->whitelist[] = $mime;

			if (strpos($mime, 'image/') === 0)
			{
				$this->images[] = $mime;
			}
		}
	}

	/**
	 * Adds multiple mime types to the whitelist
	 *
	 * @param array $mimes An array of MIME types to add to the whitelist
	 * @return void
	 */
	public function addMimeTypes(array $mimes)
	{
		foreach ($mimes as $mime)
		{
			$this->addMimeType($mime);
		}
	}

	/**
	 * Returns the whitelist of MIME Types
	 *
	 * @return array An array of MIME types that are on the whitelist
	 */
	public function getWhitelist()
	{
		return $this->whitelist;
	}
	
	// IVANO
	
	public function getMimeType($path){
		
		$mime_types = array(

			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			'docx' => 'application/msword',
			'xlsx' => 'application/vnd.ms-excel',
			'pptx' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet'
		);
		
		$ext = strtolower(array_pop(explode('.', $path)));

		if (function_exists('mime_content_type')) 
		{
			$mime = mime_content_type($path);

		} elseif (function_exists('finfo_open')) {

			$finfo = finfo_open(FILEINFO_MIME_TYPE);

			if ($finfo !== FALSE)
			{
				$fres = @finfo_file($finfo, $path);
				if ( ($fres !== FALSE)
					&& is_string($fres)
					&& (strlen($fres) > 0))
				{
					$mime = $fres;
				}

				@finfo_close($finfo);
			}

		} elseif (array_key_exists($ext, $mime_types)) {

			$mime = $mime_types[$ext];

		}else{
			
			$mime = 'application/octet-stream';
			
		}
		
		return $mime;
		
	}
	
	// IVANO
	

	/**
	 * Determines the MIME type of a file
	 *
	 * @throws Exception If the file does not exist
	 * @param string $path The full path to the file being checked
	 * @return string The MIME type of the file
	 */
	public function ofFile($path)
	{
		if ( ! file_exists($path))
		{
			throw new Exception("File " . $path . " does not exist.");
		}
		
		// IVANO
		
		// Set a default
//		$mime = 'application/octet-stream';
		
//		$finfo = finfo_open(FILEINFO_MIME_TYPE);
//
//		if ($finfo !== FALSE)
//		{
//			$fres = @finfo_file($finfo, $path);
//			if ( ($fres !== FALSE)
//				&& is_string($fres)
//				&& (strlen($fres)>0))
//			{
//				$mime = $fres;
//			}
//
//			@finfo_close($finfo);
//		}
		
		$mime = $this->getMimeType($path);
		
		//echo 'MIME: ' . $mime;
		
		// IVANO

		return $mime;
	}

	/**
	 * Determines the MIME type of a buffer
	 *
	 * @param string $buffer The buffer/data to check
	 * @return string The MIME type of the buffer
	 */
	public function ofBuffer($buffer)
	{
		
		// IVANO
		
		// Set a default
//		$mime = 'application/octet-stream';
//
//		$finfo = @finfo_open(FILEINFO_MIME_TYPE);
//		if ($finfo !== FALSE)
//		{
//			$fres = @finfo_buffer($finfo, $buffer);
//			if ( ($fres !== FALSE)
//				&& is_string($fres)
//				&& (strlen($fres)>0))
//			{
//				$mime = $fres;
//			}
//
//			@finfo_close($finfo);
//		}
		
		//echo '<br>Buffer: ' . $buffer . '<bR>';
		
		$mime = $this->getMimeType($buffer);
		
		echo 'MIME: ' . $mime;
		
		// IVANO

		return $mime;
	}

	/**
	 * Determines if a file is an image or not.
	 *
	 * @throws Exception If the file does not exist
	 * @param string $path The full path to the file being checked
	 * @return bool TRUE if it is an image; FALSE if not
	 */
	public function fileIsImage($path)
	{
		return $this->isImage($this->ofFile($path));
	}

	/**
	 * Determines if a MIME type is in our list of valid image MIME types.
	 *
	 * @param string $mime The mime to check
	 * @return bool TRUE if it is an image; FALSE if not
	 */
	public function isImage($mime)
	{
		return in_array($mime, $this->images, TRUE);
	}

	/**
	 * Gets the MIME type of a file and compares it to our whitelist to see if
	 * it is safe for upload.
	 *
	 * @throws Exception If the file does not exist
	 * @param string $path The full path to the file being checked
	 * @return bool TRUE if it safe; FALSE if not
	 */
	public function fileIsSafeForUpload($path)
	{
		return $this->isSafeForUpload($this->ofFile($path));
	}

	/**
	 * Checks a given MIME type against our whitelist to see if it is safe for
	 * upload
	 *
	 * @param string $mime The mime to check
	 * @return bool TRUE if it is an image; FALSE if not
	 */
	public function isSafeForUpload($mime)
	{
		return in_array($mime, $this->whitelist, TRUE);
	}

}
// EOF

<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */

namespace Wam\ImageBundle\Image;

use Wam\AssetBundle\Asset\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Wam\AssetBundle\Asset\Directory\Directory;
use Wam\AssetBundle\Exception\WamException;

class SourceImage extends File implements ImageInterface
{

	/**
	 * source image
	 * @var mixed
	 **/
	private $source;

	/**
	 * temp directory
	 * @var Directory
	 **/
	private $tmpDir;

	/**
	 * source data
	 * @var array
	 **/
	private $params;

	/**
	 * image width
	 * @var integer
	 **/
	private $width;

	/**
	 * image height
	 * @var integer
	 **/
	private $height;

	/**
	 * Constructor
	 * @param array $source
	 * @param Directory $tmp_dir
	 * @return void
	 **/
	public function __construct($source, Directory $tmp_dir)
	{
		$this->setSource($source);
		$this->setTmpDir($tmp_dir);
		$this->getSourceData();

		parent::__construct($this->params['path'], true);
		$this->moveToTmpDir();

		$this->setSourceSizes();
	}

	/**
	 * set source
	 * @param mixed $source
	 * @return void
	 **/
	public function setSource($source)
	{
		$this->source = $source;
	}

	/**
	 * get data from source image
	 * @return void
	 **/
	protected function getSourceData()
	{
		if($this->source instanceof UploadedFile) {
			$this->setParams(array(
				'name' => $this->source->getClientOriginalName(),
				'path' => $this->source->getPath() . '/' . $this->source->getClientOriginalName(),
				'extension' => $this->source->getExtension(),
				'mime' => $this->source->getMimeType()
			));
		} else if(is_string($this->source)) {
			if(file_exists($this->source)) {
				$this->setParams(array(
					'name' => basename($this->source),
					'path' => $this->source,
					'extension' => substr(strrchr($this->source, '.'), 1),
					'mime' => finfo_file(finfo_open(), $this->source, FILEINFO_MIME_TYPE)
				));
			}
		}

		if(!$this->getParams()) {
			throw new \InvalidArgumentException('Invalid image data provided');
		}
	}

	/**
	 * set temporary directory
	 * @param Directory $directory
	 * @return void
	 **/
	protected function setTmpDir(Directory $directory)
	{
		$this->tmpDir = $directory;
	}

	/**
	 * get tmp dir
	 * @return Directory $directory
	 **/
	public function getTmpdir()
	{
		return $this->tmpDir;
	}

	/**
	 * set params from source image
	 * @param array $params
	 * @return void
	 **/
	private function setParams($params)
	{
		$this->params = $params;
	}

	/**
	 * get params
	 * @return array
	 **/
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * move to tmp dir
	 * @return void
	 **/
	public function moveToTmpDir()
	{
		if($this->source instanceof UploadedFile) {
			$this->source->move($this->getTmpDir()->getRootPath());
		} else if(is_string($this->source)) {
			copy($this->params['path'], $this->getTmpDir()->getRootPath() . '/' . $this->params['name']);
		}
	}

	public function getTmpPath()
	{
		return $this->getTmpDir()->getRootPath() . '/' . $this->params['name'];
	}

	/**
	 * set source sizes
	 * @return integer
	 **/
	public function setSourceSizes()
	{
		$sizes = getimagesize($this->getTmpPath());
		if($sizes) {
			$this->width = $sizes[0];
			$this->height = $sizes[1];
		}
	}

	/**
	 * get width
	 * @return integer
	 **/
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * get height
	 * @return integer
	 **/
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * get mime
	 * @return string
	 **/
	public function getMime()
	{
		return (array_key_exists('mime', $this->params)) ? $this->params['mime'] : false;
	}
	
	
	

}
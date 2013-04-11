<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */

namespace Wam\ImageBundle\Actions;

use Wam\ImageBundle\Actions\ActionsInterface;

class Resize extends AbstractActions implements ActionsInterface
{
	/**
	 * output
	 * @var resource
	 **/
	private $output;

	/**
	 * string
	 * @var string
	 **/
	private $method;

	/**
	 * output width
	 * @var int
	 **/
	private $outputWidth;

	/**
	 * output height
	 * @var int
	 **/
	private $outputHeight;

	/**
	 * width resize method
	 **/
	const WIDTH = 'width';

	/**
	 * height resize method
	 **/
	const HEIGHT = 'height';

	/**
	 * square
	 **/
	const SQUARE = 'square';
	

	/**
	 * load method
	 * @return void
	 **/
	public function execute()
	{
		switch($this->getMethod()) {
			case 'width':
				if($this->outputHeight == 0) {
					$this->outputHeight = $this->scaleToWidth();
				}
				break;
			case 'height':
				if($this->outputWidth == 0) {
					$this->outputWidth = $this->scaleToHeight();
				}
				break;
			case 'square':
				$this->outputHeight = $this->outputWidth;
				break;
		}

		$this->doResize();

		return $this->getOutputName();
	}

	/**
	 * do resize
	 * @return void
	 **/
	protected function doResize()
	{
		$this->output = imagecreatetruecolor($this->outputWidth, $this->outputHeight);
		imagecopyresampled($this->output, $this->getImageResource(), 0, 0, 0, 0, $this->outputWidth, $this->outputHeight, $this->getSourceWidth(), $this->getSourceHeight());

		$this->save();
	}

	/**
	 * get source image as a resource
	 * @return Resource
	 **/
	private function getImageResource()
	{
		$resource = false;

		switch($this->getSource()->getMime()) {
			case 'image/jpeg':
			case 'image/pjpeg':
				$resource = imagecreatefromjpeg($this->getSource()->getTmpPath());
				break;
			case 'image/gif':
				$resource = imagecreatefromgif($this->getSource()->getTmpPath());
				break;
			case 'image/png':
				$resource = imagecreatefrompng($this->getSource()->getTmpPath());
				break;
		}

		return $resource;
	}
	

	/**
	 * set output width
	 * @param integer @width
	 * @return void
	 **/
	public function setOutputWidth($width)
	{
		$this->outputWidth = $width;
	}

	/**
	 * set output height
	 * @param integer @height
	 * @return void
	 **/
	public function setOutputHeight($height)
	{
		$this->outputHeight = $height;
	}

	/**
	 * set method
	 * @param string $method
	 * @return void
	 **/
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 * get method
	 * @return string
	 **/
	public function getMethod()
	{
		return $this->method;
	}
	

	/**
	 * get output
	 * @return Resource
	 **/
	public function getOutput()
	{
		return $this->output;
	}

	/**
	 * scale to width
	 * @return integer
	 **/
	public function scaleToWidth()
	{
		$ratio = $this->getSourceHeight() / $this->getSourceWidth();
		return ceil($this->outputWidth * $ratio);
	}

	/**
	 * scale to height
	 * @return integer
	 **/
	public function scaleToHeight()
	{
		$ratio = $this->getSourceWidth() / $this->getSourceHeight();
		return ceil($this->outputHeight * $ratio);
	}
	
	
	
	
	
}
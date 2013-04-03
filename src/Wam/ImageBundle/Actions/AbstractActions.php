<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */

namespace Wam\ImageBundle\Actions;

use Wam\ImageBundle\Image\SourceImage;
use Wam\AssetBundle\Entity\Base\AbstractEntity;
use Wam\AssetBundle\Asset\Directory\Directory;

abstract class AbstractActions
{
	/**
	 * source
	 * @var SourceImage
	 **/
	protected $source;

	/**
	 * source width
	 * @var float
	 **/
	protected $sourceWidth;

	/**
	 * source height
	 * @var float
	 **/
	protected $sourceHeight;

	/**
	 * source type
	 * @var 
	 **/
	protected $sourceType;

	/**
	 * Entity
	 * @var Wam\AssetBundle\Entity\Base\AbstractEntity
	 **/
	protected $entity;

	/**
	 * output format
	 * @var string (jpg, gif, png)
	 **/
	protected $outputFormat;

	/**
	 * output destiantion directory
	 * @var Wam\AssetBundle\Asset\DirectoryDirectory
	 **/
	private $destinationDirectory;

	/**
	 * output name
	 * @var string
	 **/
	private $outputName;
	

	/**
	 * Output image quality
	 */
	const OUTPUT_QUALITY = 90;

	/**
	 * format jpg
	 * @var string
	 **/
	const FORMAT_JPG = 'jpg';

	/**
	 * format gif
	 * @var string
	 **/
	const FORMAT_GIF = 'gif';

	/**
	 * format png
	 * @var string
	 **/
	const FORMAT_PNG = 'png';


	/**
	 * construtor 
	 * @param SourceImage $image
	 * @return void
	 **/
	public function __construct(SourceImage $image)
	{
		$this->setSource($image);
		$this->setSourceDimensions();

		$this->setOutputFormat(self::FORMAT_JPG);
	}

	/**
	 * set source
	 * @param SourceImage $image
	 * @return void
	 **/
	protected function setSource(SourceImage $image)
	{
		$this->source = $image;
	}

	/**
	 * get source
	 * @return SourceImage $image
	 **/
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * set output format
	 * @param string $format
	 * @return void
	 **/
	public function setOutputFormat($format)
	{
		$this->outputFormat = $format;
	}

	/**
	 * get output format
	 * @return string
	 **/
	public function getOutputFormat()
	{
		return $this->outputFormat;
	}
	

	/**
	 * set entity
	 * @param Wam\AssetBundle\Entity\Base\AbstractEntity
	 * @return void
	 **/
	public function setEntity(AbstractEntity $entity)
	{
		$this->entity = $entity;
	}

	/**
	 * get entity
	 * @return Wam\AssetBundle\Entity\Base\AbstractEntity
	 **/
	public function getEntity()
	{
		return $this->entity;
	}

	/**
	 * get source dimensions
	 * @return array
	 **/
	protected function getSourceDimensions()
	{
		return array(
			'0' => $this->getSourceWidth(),
			'1' => $this->getSourceHeight()
		);
	}

	/**
	 * set source dimensions
	 * @return void
	 **/
	private function setSourceDimensions()
	{
		$this->sourceWidth = $this->source->getWidth();
		$this->sourceHeight = $this->source->getHeight();
	}
	

	/**
	 * get source width
	 * @return float
	 **/
	public function getSourceWidth()
	{
		return $this->sourceWidth;
	}

	/**
	 * get source height
	 * @return float
	 **/
	public function getSourceHeight()
	{
		return $this->sourceHeight;
	}

	/**
	 * set destination directory
	 * @param Wam\AssetBundle\Asset\DirectoryDirectory $directory
	 * @return void
	 **/
	public function setDestinationDirectory(Directory $directory)
	{
		$this->destinationDirectory = $directory;
	}

	/**
	 * get destination directory
	 * @return Wam\AssetBundle\Asset\DirectoryDirectory $directory
	 **/
	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	/**
	 * set output name
	 * @param string $name
	 * @return void
	 **/
	public function setOutputName($name)
	{
		$this->outputName = $name;
	}

	/**
	 * get output name
	 * @return string
	 **/
	protected function getOutputName()
	{
		return $this->outputName;
	}

	/**
	 * save image*
	 * @return void
	 **/
	protected function save()
	{
		if(!$this->getOutputName()) {
			$this->setOutputName(uniqid() . '.' . $this->getOutputFormat());
		}

		switch($this->getOutputFormat()) {
			case self::FORMAT_JPG:
				$output = imagejpeg($this->getOutput(), $this->getOutputDestination(), self::OUTPUT_QUALITY);
				break;
			case self::FORMAT_GIF:
				$output = imagegif($this->getOutput(), $this->getOutputDestination());
				break;
			case self::FORMAT_PNG:
				// invert quality because stupidly, imagepng goes from 0-9, not 0-100
				$quality = 9 - (round((self::OUTPUT_QUALITY/100)*5));
				imagepng($this->getOutput(), $this->getOutputDestination());
				break;
		}

		return $this->getOutputName();
	}

	/**
	 * get output destination
	 * @return string
	 **/
	protected function getOutputDestination()
	{
		return $this->getDestinationDirectory()->getRootPath() . '/' . $this->getOutputName();
	}
	
	

}
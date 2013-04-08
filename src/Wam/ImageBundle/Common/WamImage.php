<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */

namespace Wam\ImageBundle\Common;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Wam\ImageBundle\Container\AbstractContainerAware;
use Wam\AssetBundle\Exception\WamException;
use Wam\ImageBundle\Image\SourceImage;
use Wam\AssetBundle\Asset\Directory\Directory;
use Wam\ImageBundle\Actions\Resize;
use Wam\AssetBundle\Entity\Base\AbstractEntity;

class WamImage extends AbstractContainerAware
{

	/**
	 * Kernel
	 * @var AppKernel
	 **/
	private $kernel;

	/**
	 * root directory of symfony installation
	 * @var string
	 **/
	private $rootDir;

	/**
	 * Temp image directory
	 * @var string
	 **/
	private $tmpDir;
	
	/**
	 * source image
	 * @var Wam\ImageBundle\Image\SourceImage
	 **/
	private $source;

	/**
	 * resizer object
	 * @var Wam\ImageBundle\Action\Resize
	 **/
	private $resize;

	/**
	 * entity to resize
	 * @var Wam\AssetBundle\Entity\Base\AbstractEntity
	 **/
	private $entity;
	
	

	/**
	 * initialize
	 * @return void
	 **/
	public function initialize()
	{
		$this->kernel = $this->getContainer()->get('kernel');
		$this->rootDir = realpath($this->kernel->getRootDir() . '/../');
		$_SERVER['KERNEL_ROOT_PATH'] = $this->rootDir;

		$this->setTmpDir();
	}

	/**
	 * load source image
	 * @param mixed $source_image
	 * @return this
	 **/
	public function load($source_image)
	{
		if($source_image instanceof UploadedFile) {
			$this->setSourceImage(new SourceImage($source_image, $this->getTmpDir()));
		} else if(is_array($source_image)) {
			$this->setSourceImage(new SourceImage($source_image, $this->getTmpDir()));
		} else if(is_string($source_image)) {
			$this->setSourceImage(new SourceImage($source_image, $this->getTmpDir()));
		} else {
			throw new \InvalidArgumentException('Invalid image path');
		}

		return $this->getSourceImage();
	}

	/**
	 * get source image
	 * @return SourceImage
	 **/
	public function getSourceImage()
	{
		return $this->source;
	}

	/**
	 * set source image
	 * @param SourceImage $source
	 * @return void
	 **/
	protected function setSourceImage(SourceImage $source)
	{
		$this->source = $source;
	}

	/**
	 * set temp directory
	 * @return void
	 **/
	private function setTmpDir()
	{
		$dir = $this->kernel->getContainer()->getParameter('wam_tmp_dir');

		$arr = explode($this->kernel->getContainer()->getParameter('kernel.root_dir') . '/../', $dir);
		$arr = array_filter($arr, 'strlen');

		$this->tmpDir = new Directory(array_shift($arr));

		if(!$this->tmpDir->exists()) {
			$this->tmpDir->create();
		}
	}

	/**
	 * get tmp directory
	 * @return Wam\AssetBundle\Asset\Directory\Directory
	 **/
	public function getTmpDir()
	{
		return $this->tmpDir;
	}
	
	/**
	 * init and return resizer object
	 * @param Wam\AssetBundle\Entity\Base\AbstractEntity $entity
	 * @return Wam\ImageBundle\Actions\Resize
	 **/
	public function resize(AbstractEntity $entity)
	{
		$this->entity = $entity;

		$this->resize = new Resize($this->getSourceImage());
		$this->resize->setEntity($entity);
		
		return $this;
	}

	/**
	 * do resize
	 * @return void
	 **/
	public function execute()
	{
		foreach($this->entity->getSizeDirs() as $size => $values) {
			$this->resize->setMethod($values['method']);
			$this->resize->setOutputWidth($values['width']);
			$this->resize->setOutputHeight($values['height']);
			$this->resize->setDestinationDirectory($values['directory']);
			$this->resize->execute();
		}
	}

	/**
	 * get resizer
	 * @return Wam\ImageBundle\Action\Resize
	 **/
	public function getResizer()
	{
		return $this->resize;
	}
	
	
	


}
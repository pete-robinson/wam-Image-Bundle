<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */
 
require_once __DIR__ . '/../WamImageTestCase.php';

use Symfony\Component\Console\Tester\CommandTester;
use Wam\AssetBundle\Asset\Directory\Directory;
use Wam\ImageBundle\Image\SourceImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Acme\TestBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Wam\AssetBundle\Command\BuildCommand;

class ResizeTest extends WamImageTestCase
{
	/**
	 * Wam instance
	 * @var Wam\ImageBundle\Common\WamImage
	 **/
	private $wam;

	/**
	 * wam file
	 * @var Wam\ImageBundle\Image\SourceImage
	 **/
	private $wamFile;

	/**
	 * resize object
	 * @var Wam\ImageBundle\Actions\Resize
	 **/
	private $resizer;

	/**
	 * wam entity
	 * @var 
	 **/
	private $wamEntity;
	

	/**
	 * setup function - init wam sevice
	 * @return void
	 **/
	public function setUp()
	{
		parent::setUp();

		$this->wam = $this->client->getContainer()->get('wamimage');

		$this->wamFile = $this->wam->load($this->getFile());

		copy($this->wamFile->getTmpdir()->getRootPath() . '/' . $this->wamFile->getName(), __DIR__ . '/../../tmp/files/logo.jpg');
		chmod(__DIR__ . '/../../tmp/files/logo.jpg', 0777);

	}
	
	

	/**
	 * return standard uploaded file
	 * @return Symfony\Component\HttpFoundation\File\UploadedFile
	 **/
	private function getFile()
	{
		$file = new UploadedFile(
			__DIR__ . '/../../tmp/files/logo.jpg',
			'logo.jpg',
			'image/jpeg',
			filesize(realpath(__DIR__ . '/../../tmp/files/logo.jpg'))
		);

		$response = $this->client->request(
			'POST',
			'/test',
			array('submit' => 'yes'),
			array('file' => $file)
		);

		return $this->client->getRequest()->files->get('file');
	}

	/**
	 * teardown
	 * @return void
	 **/
	protected function tearDown()
	{
		if(!file_exists(__DIR__ . '/../../tmp/files/logo.jpg')) {
			$dir = $this->getDirectory();
			copy($dir->getRootPath() . '/logo.jpg', __DIR__ . '/../../tmp/files/logo.jpg');
		}
	}

	/**
	 * get Directory
	 * @return Wam\AssetBundle\Asset\Directory\Directory
	 **/
	private function getDirectory()
	{
		$dir = $this->client->getContainer()->getParameter('wam_tmp_dir');
		$d = str_replace($this->client->getContainer()->get('kernel')->getRootDir() . '/..', '', $dir);
		return new Directory($d);
	}

	/**
	 * get Product for test data
	 * @return Acme\TestBundle\Entity\Product
	 **/
	private function getProduct($id)
	{
		return $this->client->getContainer()->get('doctrine')->getManager()->getRepository('AcmeTestBundle:Product')->find($id);
	}

	/**
	 * get Resizer
	 * @return Wam\ImageBundle\Actions\Resize
	 **/
	private function getResizer()
	{
		if(!$this->resizer) {
			$wamEntity = $this->container->get('wam')->load($this->getProduct(1));
			$this->container->get('wamimage')->load($this->getFile());
			$this->resizer = $this->container->get('wamimage')->resize($wamEntity);
		}

		return $this->resizer;
	}
	
	
	
	

}
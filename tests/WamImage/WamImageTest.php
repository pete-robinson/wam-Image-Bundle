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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Wam\AssetBundle\Asset\Directory\Directory;

class WamImageTest extends WamImageTestCase
{

	/**
	 * Wam instance
	 * @var Wam\ImageBundle\Common\WamImage
	 **/
	private $wam;

	/**
	 * setup function - init wam sevice
	 * @return void
	 **/
	public function setUp()
	{
		parent::setUp();
		// remove tmp dir so we can test that it's created successfully
		$dir = $this->client->getContainer()->getParameter('wam_tmp_dir');
		`rm -R {$dir}`;

		$this->wam = $this->client->getContainer()->get('wamimage');
	}

	public function testServiceLoaded()
	{
		$config = $this->client->getContainer()->getParameter('wam_tmp_dir');

		$this->assertInstanceOf('Wam\ImageBundle\Common\WamImage', $this->client->getContainer()->get('wamimage'));

		$this->assertEquals($config, '/Users/pete.robinson/Sites/wam/image/tests/SupportFiles/app/../web/tmp');
	}
	

	/**
	 * test wam is initialised successfully
	 * @return void
	 **/
	public function testWamImageInit()
	{
		$this->assertInstanceOf('Wam\ImageBundle\Common\WamImage', $this->wam);
	}

	/**
	 * test tmp dir param
	 * @return void
	 **/
	public function testTmpDir()
	{
		$dir = $this->wam->getTmpDir();

		$this->assertInstanceOf('Wam\AssetBundle\Asset\Directory\Directory', $dir);

		$this->assertTrue($dir->exists());

		$this->assertEquals($dir->getRootPath(), realpath($this->client->getContainer()->getParameter('wam_tmp_dir')));
	}

	/**
	 * test load image from uploaded file
	 *
	 * @return void
	 **/
	public function testLoadImageFromUploadedFileObject()
	{
		$file = $this->getFile();
		
		$wam_file = $this->wam->load($file);

		$this->assertInstanceOf('Wam\ImageBundle\Image\SourceImage', $wam_file);

		$params = $wam_file->getParams();

		$this->assertEquals('logo.jpg', $params['name']);
		$this->assertEquals('image/jpeg', $params['mime']);
		$this->assertEquals(__DIR__ . '/../../tmp/files/logo.jpg', $params['path']);

		copy($wam_file->getTmpPath(), $wam_file->getRootPath());
		chmod($wam_file->getRootPath(), 0777);
	}
	

	/**
	 * test load image from path
	 * @return void
	 **/
	public function testLoadImageFromFilePath()
	{
		$file = __DIR__ . '/../../tmp/files/logo.jpg';

		$wam_file = $this->wam->load($file);

		$this->assertInstanceOf('Wam\ImageBundle\Image\SourceImage', $wam_file);

		$params = $wam_file->getParams();

		$this->assertEquals('logo.jpg', $params['name']);
		$this->assertEquals('image/jpeg', $params['mime']);
		$this->assertEquals(__DIR__ . '/../../tmp/files/logo.jpg', $params['path']);
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
	 * test load image from path
	 * @return void
	 **/
	public function testExceptionThrownForInvalidType()
	{
		$this->setExpectedException('\InvalidArgumentException');

		$file = new StdClass();
		$file->path = __DIR__ . '/../../tmp/files/logofdsfffs.jpg';

		$wam_file = $this->wam->load($file);
	}

	/**
	 * teardown
	 * @return void
	 **/
	protected function tearDown()
	{
		if(!file_exists(__DIR__ . '/../../tmp/files/logo.jpg')) {
			$dir = $this->client->getContainer()->getParameter('wam_tmp_dir');
			$directory = new Directory($dir, true);
			copy($directory->getRootPath() . '/logo.jpg', __DIR__ . '/../../tmp/files/logo.jpg');
		}
	}
	

}
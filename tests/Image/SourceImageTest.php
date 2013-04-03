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

class SourceImageTest extends WamImageTestCase
{
	/**
	 * Wam instance
	 * @var Wam\ImageBundle\Common\WamImage
	 **/
	private $wam;

	/**
	 * HTTP client
	 * @var Symfony\Bundle\FrameworkBundle\Client
	 **/
	private $httpClient;

	/**
	 * setup function - init wam sevice
	 * @return void
	 **/
	public function setUp()
	{
		parent::setUp();
		
		$this->wam = $this->client->getContainer()->get('wamimage');
	}

	/**
	 * test an exception is thrown if an empty source type is passed
	 * @return void
	 **/
	public function testExceptionthrownIfEmptyValuePassed()
	{
		$this->setExpectedException('\InvalidArgumentException');
		$params = array();

		$wam_file = $this->wam->load($params);
	}

	/**
	 * test get tmp dir
	 * @return void
	 **/
	public function testGetTempDir()
	{
		$file = $this->getFile();

		$wam_file = $this->wam->load($file);

		$dir = $this->getDirectory();

		$this->assertInstanceOf('Wam\AssetBundle\Asset\Directory\Directory', $wam_file->getTmpDir());

		$this->assertEquals(realpath($dir->getRootPath()), $wam_file->getTmpDir()->getRootPath());
	}

	/**
	 * test get tmp dir
	 * @return void
	 **/
	public function testMoveToTmpDir()
	{
		$dir = $this->getDirectory();

		$wam_file = $this->wam->load($this->getFile());
		
		$params = $wam_file->getParams();

		$this->assertFileExists($wam_file->getTmpdir()->getRootPath() . '/' . $params['name']);

		copy($wam_file->getTmpdir()->getRootPath() . '/' . $params['name'], __DIR__ . '/../../tmp/files/logo.jpg');
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
	 * get Directory
	 * @return Wam\AssetBundle\Asset\Directory\Directory
	 **/
	private function getDirectory()
	{
		$dir = $this->client->getContainer()->getParameter('wam_tmp_dir');
		return new Directory($dir, true);
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
	
	
	

}
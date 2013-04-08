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

class AbstractActionsTest extends WamImageTestCase
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
	 * check that image bundle interfaces with asset bundle
	 *
	 * @return void
	 **/
	public function testWamImageInterfacesWithAssetBundle()
	{
		$this->application->add(new BuildCommand());

		$command = $this->application->find('wam:build');

		$tester = new CommandTester($command);

		$tester->execute(array(
			'command' => $command->getName(),
			'entity' => 'Acme\TestBundle\Entity\Product'
		));

		$this->assertContains('Congratulations, the WamEntity was created successfully', $tester->getDisplay());

		$this->assertfileExists(__DIR__ . '/../SupportFiles/src/Acme/TestBundle/WamEntity');

		$this->assertTrue(is_writable(__DIR__ . '/../SupportFiles/src/Acme/TestBundle/WamEntity'));
	}

	/**
	 * test that the resizer is loaded correctly
	 * @return void
	 **/
	public function testLoadResizer()
	{
		$resizer = $this->getResizer();
		$this->assertInstanceOf('Wam\ImageBundle\Actions\Resize', $resizer);
	}

	/**
	 * test that getSource() in resizer returns the original SourceImage object
	 * @return void
	 **/
	public function testGetSourceReturnsSourceImage()
	{
		$resizer = $this->getResizer();
		$this->assertInstanceOf('Wam\ImageBundle\Image\SourceImage', $resizer->getSource());
	}

	/**
	 * test dimensions are accurate
	 * @return void
	 **/
	public function testDimensionsAreSet()
	{
		$resizer = $this->getResizer();

		$this->assertGreaterThan(0, $resizer->getSourceWidth());
		$this->assertGreaterThan(0, $resizer->getSourceHeight());


		$dimensions = new ReflectionMethod('Wam\ImageBundle\Actions\Resize', 'getSourceDimensions');
		$dimensions->setAccessible(true);
		$this->assertEquals(array(
			'0' => $resizer->getSourceWidth(),
			'1' => $resizer->getSourceHeight(),
		), $dimensions->invoke($resizer));
	}

	/**
	 * test entity returned by resizer is the same as the entity fetched via wam
	 * @return void
	 **/
	public function testEntityReturnedByResizerIsSameAsEntityFectchedViaWam()
	{
		$wamEntity = $this->container->get('wam')->load($this->getProduct(1));
		$resizer = $this->getResizer();
		
		$this->assertEquals($resizer->getEntity(), $wamEntity);
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
			$wamEntity->create();
			$this->container->get('wamimage')->load($this->getFile());
			$this->resizer = $this->container->get('wamimage')->resize($wamEntity);
		}

		return $this->resizer->getResizer();
	}
	
	
	
	

}
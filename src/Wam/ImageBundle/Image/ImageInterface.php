<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */

namespace Wam\ImageBundle\Image;

interface ImageInterface
{

	/**
	 * get source image width
	 * @return integer
	 **/
	public function getWidth();

	/**
	 * get source image height
	 * @return integer
	 **/
	public function getHeight();


}
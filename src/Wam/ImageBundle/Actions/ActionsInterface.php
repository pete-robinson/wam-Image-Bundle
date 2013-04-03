<?php
/*
 * This file is part of the Wam Web Asset Manager Package
 *
 * (c) Pete Robinson <work@pete-robinson.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE file
 */

namespace Wam\ImageBundle\Actions;

interface ActionsInterface
{

	/**
	 * execute
	 * @return void
	 **/
	public function execute();

	/**
	 * get output
	 * @return Resource
	 **/
	public function getOutput();
	

}
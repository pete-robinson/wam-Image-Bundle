<?php
namespace Acme\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
	
	/**
	 * List categories action
	 * @param Request
	 **/
    public function testAction(Request $request)
	{
		return new Response($request->files->get('file'));
	}
	
}
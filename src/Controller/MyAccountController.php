<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MyAccountController extends AbstractController
{
	/**
	 * @Route("/my-account", name="my_account")
	 */
	public function index(LoggerInterface $logger)
	{
		$logger->info('This is not an error');

		return $this->render('my_account/index.html.twig', [
			'controller_name' => 'MyAccountController',
		]);
	}
}

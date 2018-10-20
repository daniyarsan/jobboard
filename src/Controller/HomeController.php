<?php
/**
 * Created by PhpStorm.
 * User: daniyar
 * Date: 20.10.2018
 * Time: 15:09
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 */

	public function index()
	{
		return $this->render('home/index.html.twig');
	}
}
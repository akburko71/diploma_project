<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    /**
     * @Route("/", name="app_main")
     */
    public function index(): Response
    {
        return $this->render('layouts/index.html.twig');
    }

    /**
     * @Route("/try", name="app_try")
     */
    public function try(): Response
    {
        return $this->render('layouts/try.html.twig');
    }
}

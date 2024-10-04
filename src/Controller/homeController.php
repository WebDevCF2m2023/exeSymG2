<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class homeController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route(path: '/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('bloscot-master/about.html.twig');
    }

    #[Route(path: '/blog', name: 'blog')]
    public function blog(): Response
    {
        return $this->render('bloscot-master/blog.html.twig');
    }

    #[Route(path: '/features', name: 'features')]
    public function features(): Response
    {
        return $this->render('bloscot-master/features.html.twig');
    }

    #[Route(path: '/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('bloscot-master/contact.html.twig');
    }

    
}
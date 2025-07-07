<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Serve the static index.html file
        $indexPath = $this->getParameter('kernel.project_dir') . '/public/index.html';
        
        if (file_exists($indexPath)) {
            $content = file_get_contents($indexPath);
            return new Response($content, 200, ['Content-Type' => 'text/html']);
        }
        
        return new Response('Welcome to Brand Top List API', 200, ['Content-Type' => 'text/plain']);
    }
}

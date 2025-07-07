<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CorsController extends AbstractController
{
    #[Route('/api/{path}', name: 'api_cors_preflight', methods: ['OPTIONS'], requirements: ['path' => '.+'])]
    public function preflight(): Response
    {
        return new Response('', 200, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, CF-IPCountry, X-Requested-With',
            'Access-Control-Max-Age' => '3600',
        ]);
    }
}

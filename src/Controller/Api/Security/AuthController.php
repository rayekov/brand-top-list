<?php

namespace App\Controller\Api\Security;

use App\Controller\Api\Shared\AbstractBaseApiController;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth')]
#[OA\Tag(name: 'Authentication')]
class AuthController extends AbstractBaseApiController
{
    #[Rest\Post("/login", name: "api_auth_login")]
    #[OA\Post(
        path: "/api/auth/login",
        summary: "Admin login",
        description: "Authenticate admin user and receive JWT token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "username", type: "string", example: "admin"),
                    new OA\Property(property: "password", type: "string", example: "admin123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
                        new OA\Property(property: "refresh_token", type: "string", example: "def50200...")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials")
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        // This method can be blank - it will be intercepted by the guard authenticator
        throw new \LogicException('This method can be blank - it will be intercepted by the guard authenticator.');
    }
}

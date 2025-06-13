<?php

namespace App\Controller\Api\Brand;

use App\Controller\Api\Shared\AbstractBaseApiController;
use App\Service\Brand\Interface\IBrandService;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
#[OA\Tag(name: 'Brands')]
class BrandController extends AbstractBaseApiController
{
    private IBrandService $service;

    public function __construct(IBrandService $service)
    {
        $this->service = $service;
    }

    #[Rest\Get("/brands", name: "api_brands_list")]
    #[OA\Get(
        path: "/api/brands",
        summary: "Get all brands",
        responses: [
            new OA\Response(response: 200, description: "List of brands")
        ]
    )]
    public function list()
    {
        return $this->result($this->service->findAll());
    }

    #[Rest\Get("/brands/{uuid}", name: "api_brands_show")]
    #[OA\Get(
        path: "/api/brands/{uuid}",
        summary: "Get brand by UUID",
        parameters: [
            new OA\Parameter(name: "uuid", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Brand details"),
            new OA\Response(response: 404, description: "Brand not found")
        ]
    )]
    public function show(string $uuid)
    {
        return $this->result($this->service->findOne($uuid));
    }

    #[Rest\Post("/admin/brands", name: "api_brands_create")]
    #[OA\Post(
        path: "/api/admin/brands",
        summary: "Create a new brand (Admin only)",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "brand_name", type: "string", example: "Casino Royal"),
                    new OA\Property(property: "brand_image", type: "string", example: "https://example.com/logo.png"),
                    new OA\Property(property: "rating", type: "integer", example: 85)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Brand created"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function create(Request $request)
    {
        return $this->result($this->service->create($request));
    }

    #[Rest\Put("/admin/brands/{uuid}", name: "api_brands_update")]
    #[OA\Put(
        path: "/api/admin/brands/{uuid}",
        summary: "Update brand (Admin only)",
        parameters: [
            new OA\Parameter(name: "uuid", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "brand_name", type: "string", example: "Casino Royal Updated"),
                    new OA\Property(property: "brand_image", type: "string", example: "https://example.com/new-logo.png"),
                    new OA\Property(property: "rating", type: "integer", example: 90)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Brand updated"),
            new OA\Response(response: 404, description: "Brand not found")
        ]
    )]
    public function update(string $uuid, Request $request)
    {
        return $this->result($this->service->update($uuid, $request));
    }

    #[Rest\Delete("/admin/brands/{uuid}", name: "api_brands_delete")]
    #[OA\Delete(
        path: "/api/admin/brands/{uuid}",
        summary: "Delete brand (Admin only)",
        parameters: [
            new OA\Parameter(name: "uuid", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Brand deleted"),
            new OA\Response(response: 404, description: "Brand not found")
        ]
    )]
    public function delete(string $uuid)
    {
        return $this->result($this->service->delete($uuid));
    }
}

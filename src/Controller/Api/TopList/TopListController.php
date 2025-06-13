<?php

namespace App\Controller\Api\TopList;

use App\Controller\Api\Shared\AbstractBaseApiController;
use App\Service\TopList\Interface\ITopListService;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
#[OA\Tag(name: 'TopList')]
class TopListController extends AbstractBaseApiController
{
    private ITopListService $service;

    public function __construct(ITopListService $service)
    {
        $this->service = $service;
    }

    #[Rest\Get("/toplist", name: "api_toplist_geolocation")]
    #[OA\Get(
        path: "/api/toplist",
        summary: "Get toplist based on user's geolocation",
        description: "Returns toplist based on CF-IPCountry header. Falls back to default country or Cameroon if no geolocation is available.",
        parameters: [
            new OA\Parameter(
                name: "CF-IPCountry",
                in: "header",
                required: false,
                schema: new OA\Schema(type: "string", example: "FR"),
                description: "Cloudflare country code header (automatically set by Cloudflare)"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Toplist for user's location",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "code", type: "integer", example: 200),
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(property: "messages", type: "array", items: new OA\Items(type: "string")),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "uuid", type: "string", example: "123e4567-e89b-12d3-a456-426614174000"),
                                    new OA\Property(property: "position", type: "integer", example: 1),
                                    new OA\Property(property: "is_active", type: "boolean", example: true),
                                    new OA\Property(
                                        property: "brand",
                                        properties: [
                                            new OA\Property(property: "brand_id", type: "integer", example: 1),
                                            new OA\Property(property: "uuid", type: "string", example: "123e4567-e89b-12d3-a456-426614174001"),
                                            new OA\Property(property: "brand_name", type: "string", example: "Casino Royal"),
                                            new OA\Property(property: "brand_image", type: "string", example: "https://example.com/logo.png"),
                                            new OA\Property(property: "rating", type: "integer", example: 95)
                                        ]
                                    ),
                                    new OA\Property(
                                        property: "country",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 1),
                                            new OA\Property(property: "uuid", type: "string", example: "123e4567-e89b-12d3-a456-426614174002"),
                                            new OA\Property(property: "iso_code", type: "string", example: "FR"),
                                            new OA\Property(property: "name", type: "string", example: "France")
                                        ]
                                    )
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function getByGeolocation(Request $request)
    {
        return $this->result($this->service->getTopListByGeolocation($request));
    }

    #[Rest\Get("/toplist/{countryCode}", name: "api_toplist_country")]
    #[OA\Get(
        path: "/api/toplist/{countryCode}",
        summary: "Get toplist for specific country",
        parameters: [
            new OA\Parameter(
                name: "countryCode", 
                in: "path", 
                required: true, 
                schema: new OA\Schema(type: "string", example: "FR"),
                description: "ISO-2 country code (e.g., FR, US, CM)"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Toplist for specified country")
        ]
    )]
    public function getByCountry(string $countryCode)
    {
        return $this->result($this->service->getTopListByCountry($countryCode));
    }

    #[Rest\Post("/admin/toplist", name: "api_toplist_create")]
    #[OA\Post(
        path: "/api/admin/toplist",
        summary: "Create toplist entry (Admin only)",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "brand_uuid", type: "string", example: "123e4567-e89b-12d3-a456-426614174000"),
                    new OA\Property(property: "country_uuid", type: "string", example: "123e4567-e89b-12d3-a456-426614174001"),
                    new OA\Property(property: "position", type: "integer", example: 1),
                    new OA\Property(property: "is_active", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Toplist entry created"),
            new OA\Response(response: 400, description: "Validation error")
        ]
    )]
    public function create(Request $request)
    {
        return $this->result($this->service->createTopListEntry($request));
    }

    #[Rest\Put("/admin/toplist/{uuid}", name: "api_toplist_update")]
    #[OA\Put(
        path: "/api/admin/toplist/{uuid}",
        summary: "Update toplist entry (Admin only)",
        parameters: [
            new OA\Parameter(name: "uuid", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "position", type: "integer", example: 2),
                    new OA\Property(property: "is_active", type: "boolean", example: false)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Toplist entry updated"),
            new OA\Response(response: 404, description: "Entry not found")
        ]
    )]
    public function update(string $uuid, Request $request)
    {
        return $this->result($this->service->updateTopListEntry($uuid, $request));
    }

    #[Rest\Delete("/admin/toplist/{uuid}", name: "api_toplist_delete")]
    #[OA\Delete(
        path: "/api/admin/toplist/{uuid}",
        summary: "Delete toplist entry (Admin only)",
        parameters: [
            new OA\Parameter(name: "uuid", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Entry deleted"),
            new OA\Response(response: 404, description: "Entry not found")
        ]
    )]
    public function delete(string $uuid)
    {
        return $this->result($this->service->deleteTopListEntry($uuid));
    }
}

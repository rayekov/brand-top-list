<?php

namespace App\Controller\Api\Country;

use App\Controller\Api\Shared\AbstractBaseApiController;
use App\Service\Country\Interface\ICountryService;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[OA\Tag(name: 'Countries')]
class CountryController extends AbstractBaseApiController
{
    private ICountryService $service;

    public function __construct(ICountryService $service)
    {
        $this->service = $service;
    }

    #[Rest\Get("/countries", name: "api_countries_list")]
    #[OA\Get(
        path: "/api/countries",
        summary: "Get all countries",
        responses: [
            new OA\Response(response: 200, description: "List of countries")
        ]
    )]
    public function list()
    {
        return $this->result($this->service->findAllCountries());
    }

    #[Rest\Get("/countries/{uuid}", name: "api_countries_show")]
    #[OA\Get(
        path: "/api/countries/{uuid}",
        summary: "Get country by UUID",
        parameters: [
            new OA\Parameter(name: "uuid", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Country details"),
            new OA\Response(response: 404, description: "Country not found")
        ]
    )]
    public function show(string $uuid)
    {
        return $this->result($this->service->findCountryByUuid($uuid));
    }

    #[Rest\Get("/countries/iso/{isoCode}", name: "api_countries_by_iso")]
    #[OA\Get(
        path: "/api/countries/iso/{isoCode}",
        summary: "Get country by ISO code",
        parameters: [
            new OA\Parameter(
                name: "isoCode", 
                in: "path", 
                required: true, 
                schema: new OA\Schema(type: "string", example: "FR"),
                description: "ISO-2 country code"
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Country details"),
            new OA\Response(response: 404, description: "Country not found")
        ]
    )]
    public function getByIsoCode(string $isoCode)
    {
        return $this->result($this->service->getCountryByIsoCode($isoCode));
    }

    #[Rest\Post("/admin/countries", name: "api_countries_create")]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Post(
        path: "/api/admin/countries",
        summary: "Create a new country (Admin only)",
        security: [["Bearer" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "iso_code", type: "string", example: "FR"),
                    new OA\Property(property: "name", type: "string", example: "France"),
                    new OA\Property(property: "is_default", type: "boolean", example: false)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Country created"),
            new OA\Response(response: 400, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function create(Request $request)
    {
        return $this->result($this->service->createCountry($request));
    }

    #[Rest\Put("/admin/countries/{uuid}", name: "api_countries_update")]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Put(
        path: "/api/admin/countries/{uuid}",
        summary: "Update country (Admin only)",
        security: [["Bearer" => []]],
        parameters: [
            new OA\Parameter(name: "uuid", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "iso_code", type: "string", example: "FR"),
                    new OA\Property(property: "name", type: "string", example: "France Updated"),
                    new OA\Property(property: "is_default", type: "boolean", example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Country updated"),
            new OA\Response(response: 404, description: "Country not found")
        ]
    )]
    public function update(string $uuid, Request $request)
    {
        return $this->result($this->service->updateCountry($uuid, $request));
    }

    #[Rest\Delete("/admin/countries/{uuid}", name: "api_countries_delete")]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Delete(
        path: "/api/admin/countries/{uuid}",
        summary: "Delete country (Admin only)",
        security: [["Bearer" => []]],
        parameters: [
            new OA\Parameter(name: "uuid", in: "path", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Country deleted"),
            new OA\Response(response: 404, description: "Country not found")
        ]
    )]
    public function delete(string $uuid)
    {
        return $this->result($this->service->deleteCountry($uuid));
    }
}

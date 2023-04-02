<?php

namespace App\Tests\Feature\Product;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Manufacturer;
use App\Repository\ManufacturerRepository;
use App\Tests\Traits\CreateClientWithTokenTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    use CreateClientWithTokenTrait;

    public function testItCanNotBeCreatedUsingInvalidData(): void
    {
        $this->createClientForAdminWith(
            $token = bin2hex(random_bytes(60))
        )->request(
            'POST',
            '/api/products',
            [
                'headers' => ['x-api-token' => $token],
                'json' => [
                    'mpn' => '1234',
                    'name' => 'A test product',
                    'description' => 'A test description',
                    'issueDate' => '1985-07-31',
                    'manufacturer' => null,
                ]
            ],
        );

        $this->assertResponseIsUnprocessable();
        $this->assertResponseHeaderSame(
            'content-type',
            'application/ld+json; charset=utf-8'
        );
        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'manufacturer: This value should not be null.',
        ]);
    }

    public function testUserCanCreateAProduct(): void
    {
        $this->createClientForAdminWith(
            $token = bin2hex(random_bytes(60))
        )->request(
            'POST',
            '/api/products',
            [
                'headers' => ['x-api-token' => $token],
                'json' => [
                    'mpn' => '1234',
                    'name' => 'A test product',
                    'description' => 'A test description',
                    'issueDate' => '1985-07-31',
                    'manufacturer' => sprintf(
                        '/api/manufacturers/%d',
                        $this->getFirstManufacturer()->getId()
                    ),
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame(
            'content-type',
            'application/ld+json; charset=utf-8'
        );
        $this->assertJsonContains([
            'mpn' => '1234',
            'name' => 'A test product',
            'description' => 'A test description',
            'issueDate' => '1985-07-31T00:00:00+00:00',
        ]);
    }

    private function getFirstManufacturer(): Manufacturer
    {
        return $this->getContainer()
            ->get(ManufacturerRepository::class)
            ->findOneBy([], orderBy: ['id' => 'ASC']);
    }
}
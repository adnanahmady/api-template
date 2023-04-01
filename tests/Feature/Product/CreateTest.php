<?php

namespace App\Tests\Feature\Product;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Manufacturer;
use App\Repository\ManufacturerRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class CreateTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testItCanNotBeCreatedUsingInvalidData(): void
    {
        $response = static::createClient()->request(
            'POST',
            '/api/products',
            [
                'json' => [
                    'mpn' => '1234',
                    'name' => 'A test product',
                    'description' => 'A test description',
                    'issueDate' => '1985-07-31',
                    'manufacturer' => null,
                ]
            ]
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
        static::createClient()->request(
            'POST',
            '/api/products',
            [
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
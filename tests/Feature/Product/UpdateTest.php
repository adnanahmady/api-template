<?php

namespace App\Tests\Feature\Product;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Repository\ManufacturerRepository;
use App\Repository\ProductRepository;
use App\Tests\Traits\CreateClientWithTokenTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    use CreateClientWithTokenTrait;

    public function testItCanBeUpdated(): void
    {
        $this->client = $this->createClient();
        $this->user = $this->addUser(
            roles: ['ROLE_USER']
        );
        $id = $this->getFirstProduct()->getId();

        $this->createClientWith(
            $token = bin2hex(random_bytes(60))
        )->request(
            'PUT',
            "/api/products/$id",
            [
                'json' => [
                    'description' => 'An updated description',
                ],
                'headers' => ['x-api-token' => $token],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => '/api/products/' . $id,
            'description' => 'An updated description',
        ]);
    }

    public function testItCanNotBeUpdatedByOfferingWrongApiToken(): void
    {
        $id = $this->getFirstProduct()->getId();

        $this->createClient()->request(
            'PUT',
            "/api/products/$id",
            [
                'json' => [
                    'description' => 'An updated description',
                ],
                'headers' => ['x-api-token' => 'fake-token'],
            ]
        );

        $this->assertResponseStatusCodeSame(
            Response::HTTP_UNAUTHORIZED
        );
        $this->assertJsonContains([
            'message' => 'Invalid credentials.',
        ]);
    }

    private function getFirstProduct(): Product
    {
        $product = $this->getProductRepository()
            ->findOneBy([], orderBy: ['id' => 'ASC']);
        $product->setOwner($this->user);

        return $product;
    }

    private function getProductRepository(): ProductRepository
    {
        return $this->getContainer()
            ->get(ProductRepository::class);
    }
}
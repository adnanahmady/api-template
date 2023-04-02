<?php

namespace App\Tests\Feature\Product;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Traits\CreateClientWithTokenTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class GetCollectionTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    use CreateClientWithTokenTrait;

    public function testUserCanSetThePageOfData(): void
    {
        $this->createClientWith(
            $token = bin2hex(random_bytes(60))
        )->request(
            'GET',
            '/api/products',
            [
                'query' => ['page' => 3],
                'headers' => ['x-api-token' => $token],
            ]
        );

        $this->assertJsonContains([
            "hydra:view" => [
                "@id" => "/api/products?page=3",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/products?page=1",
                "hydra:last" => "/api/products?page=20",
                "hydra:previous" => "/api/products?page=2",
                "hydra:next" => "/api/products?page=4",
            ],
        ]);
    }

    public function testDefaultNumberOfMembersShouldExistInEachPage(): void
    {
        $response = $this->createClientWith(
            $token = bin2hex(random_bytes(60))
        )->request(
            'GET',
            '/api/products',
            ['headers' => ['x-api-token' => $token]],
        );

        $this->assertCount(
            5,
            $response->toArray()['hydra:member']
        );
    }

    public function testUserCanGetACollectionOfProducts(): void
    {
        $this->createClientWith(
            $token = bin2hex(random_bytes(60))
        )->request(
            'GET',
            '/api/products',
            ['headers' => ['x-api-token' => $token]],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame(
            'content-type',
            'application/ld+json; charset=utf-8'
        );
        $this->assertJsonContains([
            "@context" => "/api/contexts/Product",
            "@id" => "/api/products",
            "@type" => "hydra:Collection",
            "hydra:totalItems" => 100,
            "hydra:view" => [
                "@id" => "/api/products?page=1",
                "@type" => "hydra:PartialCollectionView",
                "hydra:first" => "/api/products?page=1",
                "hydra:last" => "/api/products?page=20",
                "hydra:next" => "/api/products?page=2",
            ],
        ]);
    }
}
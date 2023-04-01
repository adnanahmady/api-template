<?php

namespace App\Tests\Feature\Product;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class GetCollectionTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testUserCanSetThePageOfData(): void
    {
        static::createClient()->request(
            'GET',
            '/api/products',
            [
                'query' => [
                    'page' => 3
                ]
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
        $response = static::createClient()->request(
            'GET',
            '/api/products'
        );

        $this->assertCount(
            5,
            $response->toArray()['hydra:member']
        );
    }

    public function testUserCanGetACollectionOfProducts(): void
    {
        static::createClient()->request(
            'GET',
            '/api/products'
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
<?php

namespace App\Tests\Feature\Product;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Repository\ManufacturerRepository;
use App\Repository\ProductRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class UpdateTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testItCanBeUpdated(): void
    {
        $id = $this->getFirstProduct()->getId();

        static::createClient()->request(
            'PUT',
            sprintf(
                '/api/products/%d',
                $id
            ),
            [
                'json' => [
                    'description' => 'An updated description',
                ]
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => '/api/products/' . $id,
            'description' => 'An updated description',
        ]);
    }

    private function getFirstProduct(): Product
    {
        return $this->getContainer()
            ->get(ProductRepository::class)
            ->findOneBy([], orderBy: ['id' => 'ASC']);
    }
}
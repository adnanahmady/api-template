<?php

namespace App\Tests\Feature;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Traits\CreateClientWithTokenTrait;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ApiTokenTest extends ApiTestCase
{
    use RefreshDatabaseTrait;
    use CreateClientWithTokenTrait;

    public function testWithWriteTokenUserGetsAuthenticated(): void
    {
        $this->createClientWith(
            $token = bin2hex(random_bytes(60))
        )->request(
            'GET',
            '/api/products',
            ['headers' => ['x-api-token' => $token]]
        );

        $this->assertResponseIsSuccessful();
    }
}
<?php

namespace App\Tests\Traits;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\ApiTokenRepository;
use App\Repository\UserRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait CreateClientWithTokenTrait
{
    protected function createClientWith(
        string $token
    ): HttpClientInterface {
        $client = $this->createClient();
        $this->addApiToken($token);

        return $client;
    }
    private function addApiToken(string $token): string
    {
        $tokenOwner = $this->addUser();
        $at = new ApiToken();
        $at->setToken($token);
        $at->setTokenOwner($tokenOwner);
        $this->getTokenRepository()->save($at, true);

        return $token;
    }

    private function addUser(): User
    {
        $user = new User();
        $user->setEmail('user@email.com');
        $user->setPassword(crypt('password', 'salt'));
        $this->getUserRepository()->save($user, true);

        return $user;
    }

    private function getTokenRepository(): ApiTokenRepository
    {
        return $this->getContainer()
            ->get(ApiTokenRepository::class);
    }

    private function getUserRepository(): UserRepository
    {
        return $this->getContainer()
            ->get(UserRepository::class);
    }
}
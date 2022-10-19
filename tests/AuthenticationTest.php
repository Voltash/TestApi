<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;


class AuthenticationTest extends ApiTestCase
{

    use ReloadDatabaseTrait;

    public function testLogin(): void
    {

        $client = self::createClient();
        $container = self::getContainer();

        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setPassword(
            $container->get('security.user_password_hasher')->hashPassword($user, '12345')
        );

        $user->setRoles(['ROLE_ADMIN']);
        $manager = $container->get('doctrine')->getManager();
        $manager->persist($user);
        $manager->flush();

        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'admin@example.com',
                'password' => '12345',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/api/products');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/api/products', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}
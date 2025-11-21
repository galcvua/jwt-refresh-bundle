<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Functional;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;

final class AuthFlowTest extends WebTestCase
{
    #[RunInSeparateProcess]
    public function testLoginProvidesTokenAndAllowsAuthorizedRequest(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/login', [
            'username' => 'user@example.com',
            'password' => 'password',
        ]);

        self::assertResponseIsSuccessful();

        $data = json_decode((string) $client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('token', $data);
        $token = $data['token'];
        self::assertIsString($token);
        self::assertNotSame('', trim($token));

        $client->request(method: 'GET', uri: '/api/me', server: [
            'HTTP_Authorization' => 'Bearer '.$token,
        ]);

        self::assertResponseIsSuccessful();

        $me = json_decode((string) $client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('user@example.com', $me['user'] ?? null);
    }

    #[RunInSeparateProcess]
    public function testRefreshAndLogoutFlow(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/login', [
            'username' => 'user@example.com',
            'password' => 'password',
        ]);
        self::assertResponseIsSuccessful();

        $client->request('POST', '/token/refresh');

        self::assertResponseIsSuccessful();

        $refreshedData = json_decode((string) $client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $refreshedToken = $refreshedData['token'] ?? '';

        self::assertIsString($refreshedToken);
        self::assertNotSame('', trim($refreshedToken));

        $client->request('POST', '/logout');
        self::assertResponseStatusCodeSame(204);

        $client->request('POST', '/token/refresh');
        self::assertResponseStatusCodeSame(401);
    }
}

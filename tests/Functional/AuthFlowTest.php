<?php

declare(strict_types=1);

namespace Galcvua\JwtRefreshBundle\Tests\Functional;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;

final class AuthFlowTest extends WebTestCase
{
    #[RunInSeparateProcess]
    public function testLoginProvidesTokenAndAllowsAuthorizedRequest(): void
    {
        $client = static::createHttpBrowser();

        $client->jsonRequest('POST', '/login', [
            'username' => 'user@example.com',
            'password' => 'password',
        ]);

        $loginResponse = $client->getResponse();
        self::assertSame(200, $loginResponse->getStatusCode());

        $data = $loginResponse->toArray();
        self::assertArrayHasKey('token', $data);
        $token = $data['token'];
        self::assertIsString($token);
        self::assertNotSame('', trim($token));

        $client->request(method: 'GET', uri: '/api/me', server: [
            'HTTP_Authorization' => 'Bearer '.$token,
        ]);

        $meResponse = $client->getResponse();
        self::assertSame(200, $meResponse->getStatusCode());

        $me = $meResponse->toArray();
        self::assertSame('user@example.com', $me['user'] ?? null);
    }

    #[RunInSeparateProcess]
    public function testRefreshAndLogoutFlow(): void
    {
        $client = static::createHttpBrowser();
        $client->jsonRequest('POST', '/login', [
            'username' => 'user@example.com',
            'password' => 'password',
        ]);
        self::assertSame(200, $client->getResponse()->getStatusCode());

        $client->request('POST', '/token/refresh');

        $refreshResponse = $client->getResponse();
        self::assertSame(200, $refreshResponse->getStatusCode());

        $refreshedData = $refreshResponse->toArray();
        $refreshedToken = $refreshedData['token'] ?? '';

        self::assertIsString($refreshedToken);
        self::assertNotSame('', trim($refreshedToken));

        $client->request('POST', '/logout');
        self::assertSame(204, $client->getResponse()->getStatusCode());

        $client->request('POST', '/token/refresh');
        self::assertSame(401, $client->getResponse()->getStatusCode());
    }
}

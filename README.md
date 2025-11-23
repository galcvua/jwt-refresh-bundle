[![Static Checks](https://github.com/galcvua/jwt-refresh-bundle/actions/workflows/static-checks.yml/badge.svg)](https://github.com/galcvua/jwt-refresh-bundle/actions/workflows/static-checks.yml)
[![Tests](https://github.com/galcvua/jwt-refresh-bundle/actions/workflows/tests.yml/badge.svg)](https://github.com/galcvua/jwt-refresh-bundle/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/galcvua/jwt-refresh-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/galcvua/jwt-refresh-bundle)

# JWT Refresh Bundle

This bundle manages refresh tokens for JWT (JSON Web Tokens) in the simplest and safest way.
It integrates with the [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle), using **HTTP-only cookies** backed by Symfony’s session system.
No Doctrine ORM/ODM or external persistence is required, and your API remains stateless for regular requests.

## Features

- Refresh JWTs **without persisting** refresh tokens.
- Logout endpoint that clears the refresh context.
- Optional OpenAPI decoration for API Platform (refresh + logout operations).

## Requirements

- PHP 8.3+
- Symfony 7.3+
- [lexik/jwt-authentication-bundle](https://github.com/lexik/LexikJWTAuthenticationBundle) ^3.1

## Installation

```bash
composer require galcvua/jwt-refresh-bundle
```

Register the bundle in config/bundles.php (Symfony Flex does this automatically):

```php
   return [
       //...
        Galcvua\JwtRefreshBundle\GalcvuaJwtRefreshBundle::class => ['all' => true],
   ];
```

## Configuration

```yaml
# config/packages/galcvua_jwt_refresh.yaml (example)
galcvua_jwt_refresh:
    open_api:
        refresh:
            enabled: true
            tags:
                - JWT Refresh
        logout:
            enabled: true
            tags:
                - JWT Refresh
```

```yaml
# config/packages/security.php (example)
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        refresh_token:
            pattern: ^/api/token/
            custom_authenticators:
                - galcvua_jwt_refresh.security.jwt_refresh_authenticator
            logout:
                path: /api/token/logout
        login:
            pattern: ^/api/login
            stateless: true
            json_login:
                check_path: /api/login_check
                success_handler: lexik_jwt_authentication.handler.authentication_success
                failure_handler: lexik_jwt_authentication.handler.authentication_failure
```

```yaml
# config/routes.yaml (example)
api_token_refresh:
    path: /api/token/refresh
    methods: POST
    controller: galcvua_jwt_refresh.controller.jwt_refresh
api_login_check:
    path: /api/login_check
    methods: POST
controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute
```

### Session Cookie Path

By default, Symfony issues the session cookie for the root path `/`.
If you want the refresh context to be isolated from the rest of your application,
you can restrict the cookie path to the refresh firewall only.

This can be done by overriding the cookie options in `framework.session`:

```yaml
# config/packages/framework.yaml
framework:
    session:
        cookie_path: '/api/token'
```

## Usage

1. Authenticate via /api/login_check to obtain an access token (from Lexik).
2. Send a POST request to /api/token/refresh to receive a new token.
3. Call /api/token/logout to clear the refresh session.

## Testing

The bundle includes unit and functional tests.
When testing your own application, avoid using mock session storage, because it does not mimic real browser behavior and may cause inconsistent results with stateless firewalls.

<?php

declare(strict_types=1);

use Galcvua\JwtRefreshBundle\GalcvuaJwtRefreshBundle;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;

return [
    FrameworkBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    LexikJWTAuthenticationBundle::class => ['all' => true],
    GalcvuaJwtRefreshBundle::class => ['all' => true],
];

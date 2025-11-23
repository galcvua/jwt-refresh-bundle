[![Static Checks](https://github.com/galcvua/jwt-refresh-bundle/actions/workflows/static-checks.yml/badge.svg)](https://github.com/galcvua/jwt-refresh-bundle/actions/workflows/static-checks.yml)
[![Tests](https://github.com/galcvua/jwt-refresh-bundle/actions/workflows/tests.yml/badge.svg)](https://github.com/galcvua/jwt-refresh-bundle/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/galcvua/jwt-refresh-bundle/branch/master/graph/badge.svg)](https://codecov.io/gh/galcvua/jwt-refresh-bundle)

# JWT Refresh Bundle

The purpose of this bundle is to manage refresh tokens for JWT (JSON Web Tokens) in the easiest possible way.
It integrates with the excellent [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
 and doesn’t require Doctrine ORM/ODM, because it uses Symfony’s built-in session system while still keeping your API stateless.

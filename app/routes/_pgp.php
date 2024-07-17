<?php

use App\Middleware\AuthMiddleware;

$middleware = new AuthMiddleware();

app()->group('/pgp', ['middleware' => [$middleware, 'handle'], function () {
    app()->match('GET', '/key', "PgpController@get_public_key");
}]);

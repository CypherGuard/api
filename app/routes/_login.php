<?php

use App\Middleware\AuthMiddleware;

$middleware = new AuthMiddleware();

app()->group('/vaults/login', ['middleware' => [$middleware, 'handle'], function () {
    app()->match('GET', '/{id}', "VaultController@show");
    app()->match('PUT', '/{id}', "VaultController@update");
    app()->match('DELETE', '/{id}', "VaultController@destroy");
    app()->match('POST', '/', "VaultController@store");
    app()->match('GET', '/', "VaultController@index");
}]);

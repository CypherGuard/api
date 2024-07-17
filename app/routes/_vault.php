<?php

use App\Middleware\AuthMiddleware;

$middleware = new AuthMiddleware();

app()->group('/vaults', ['middleware' => [$middleware, 'handle'], function () {
    app()->match('POST', '/share/{id}', "VaultController@add_user");
    app()->match('DELETE', '/share/{id}', "VaultController@remove_user");
    app()->match('GET', '/share/{id}', "VaultController@get_user");

    app()->match('POST', '/transfer/{id}', "VaultController@transfer_to_user");
    app()->match('GET', '/{id}', "VaultController@show");
    app()->match('PUT', '/{id}', "VaultController@update");
    app()->match('DELETE', '/{id}', "VaultController@destroy");
    app()->match('POST', '/', "VaultController@store");
    app()->match('GET', '/', "VaultController@index");
}]);

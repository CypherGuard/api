<?php

use App\Middleware\AuthMiddleware;

$middleware = new AuthMiddleware();

app()->group('/vaults/{vid}/login', ['middleware' => [$middleware, 'handle'], function () {
//    app()->match('GET', '/{id}', "LoginController@show");
//    app()->match('PUT', '/{id}', "LoginController@update");
//    app()->match('DELETE', '/{id}', "LoginController@destroy");
//    app()->match('POST', '/', "LoginController@store");
    app()->match('GET', '/', "LoginController@index");
}]);

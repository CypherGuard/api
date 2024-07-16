<?php

use App\Middleware\AuthMiddleware;

$middleware = function () {
    db()->autoConnect();
    $user = auth()->user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
};

app()->group('/vaults', ['middleware' => $middleware, function () {
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

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
    app()->match('GET', '/', "VaultController@index");
    app()->match('GET', '/{id}', "VaultController@show");
    app()->match('POST', '/', "VaultController@store");
    app()->match('PUT', '/{id}', "VaultController@update");
    app()->match('DELETE', '/{id}', "VaultController@destroy");
}]);

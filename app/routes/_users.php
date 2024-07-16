<?php

use App\Middleware\AuthMiddleware;

$middleware = function () {
    db()->autoConnect();
    $user = auth()->user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
};

app()->group('/user', ['middleware' => $middleware, function () {
    app()->match('GET', '/me', "UserController@me");
}]);

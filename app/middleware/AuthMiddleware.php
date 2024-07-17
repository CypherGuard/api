<?php

namespace App\Middleware;

class AuthMiddleware {
    public function handle() {
        db()->autoConnect();
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}

<?php

namespace App\Middleware;

class AuthMiddleware {
    const check = 'check';

    public function check () {


        // This is a simple middleware that checks if the user is logged in
        // If the user is not logged in, it redirects to the login page
        if (!auth()->check()) {
            return redirect('/login');
        }
    }

}

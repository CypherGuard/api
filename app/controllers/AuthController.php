<?php

namespace App\Controllers;

use Leaf\Http\Request;

class AuthController extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function login()
    {
        $email = request()->get('email');
        $password = request()->get('password');

        $data = auth()->login([
            'email' => $email,
            'password' => $password
        ]);

        if (!$data) {
            $errors = auth()->errors();
            return response()->json(['errors' => $errors], 401);
        }

        $token = $data['token'];
        return response()->json(['token' => $token], 200);
    }

    public function register() {
        $email = request()->get('email');
        $password = request()->get('password');
        $username = request()->get('username');

        $user = auth()->register([
            'email' => $email,
            'password' => $password,
            'username' => $username,
            'fullname' => 'User '.rand(1, 1000)
        ]);

        if (!$user) {
            $errors = auth()->errors();
            return response()->json(['errors' => $errors], 401);
        }

        return response()->json(['sucess' => true]);
    }

}

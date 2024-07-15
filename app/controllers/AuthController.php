<?php

namespace App\Controllers;

use Leaf\Http\Request;

class AuthController extends Controller {
    public function __construct() {
        parent::__construct();
        db()->autoConnect();
        auth()->dbConnection(db()->connection());
        auth()->config('DB_TABLE', 'users');
        $this->request = new Request;
    }

    public function login()
    {
        $email = request()->get('email');
        $password = request()->get('password');

        $data = auth()->login([
            'username' => $email,
            'password' => $password
        ]);

        if ($data) {
            // user is authenticated
            $token = $data['token'];
            $user = $data['user'];
            return response()->json(['token' => $token, 'user' => $user]);
        } else {
            // user is not authenticated
            $errors = auth()->errors();
            return response()->json(['errors' => $errors]);
        }
    }

    public function register() {
        $email = request()->get('email');
        $password = request()->get('password');

        $user = auth()->register([
            'email' => $email,
            'password' => $password,
            'username' => 'user'.rand(1, 1000),
            'fullname' => 'User '.rand(1, 1000)
        ]);

        return response()->json(['sucess' => true]);
    }

}

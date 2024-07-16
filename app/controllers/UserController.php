<?php

namespace App\Controllers;

use App\Models\User;
use Leaf\Http\Request;

class UserController extends Controller {
    public function __construct() {
        parent::__construct();
    }

    public function me()
    {
        $user_id = auth()->user()['id'];

        $user = db()
            ->select('users', "id, username, fullname, email")
            ->where(['id' => $user_id])
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

}

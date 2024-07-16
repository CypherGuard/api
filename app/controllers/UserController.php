<?php

namespace App\Controllers;

use App\Models\User;

class UserController extends Controller {
    public function me($id)
    {
        $user_id = auth()->user()['id'];

        $user = db()
            ->select('users')
            ->where(['id' => $user_id])
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

}

<?php

namespace App\Controllers;

use Leaf\Http\Request;

class LoginController extends Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function index($vid)
    {
        $vault = db()->select('vaults')->where(['id' => $vid])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $logins = db()
            ->select('logins')
            ->where(['vault_id' => $vid])
            ->orderBy('id')
            ->fetchAll();
        return response()->json($logins);
    }

    public function show($vid, $id)
    {
        $vault = db()->select('vaults')->where(['id' => $vid])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $login = db()
            ->select('logins')
            ->where([
                'id' => $id,
                'vault_id' => $vid
            ])
            ->first();

        if (!$login) {
            return response()->json(['error' => 'Login not found'], 404);
        }

        return response()->json($login);
    }

    public function store($vid)
    {
        $user_id = auth()->user()['id'];
        $data = request()->try(['name', 'username', 'password', 'url', 'notes']);

        if (empty($data)) {
            return response()->json(['error' => 'No data provided'], 400);
        }

        $vault = db()->select('vaults')->where(['id' => $vid])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $data['vault_id'] = $vid;
        $data['owner_id'] = $user_id;

        $login = db()->insert('logins')->params($data)->execute();

        if (!$login) {
            return response()->json(['error' => 'An error occurred'], 400);
        }

        return response()->json($login);
    }

    public function update($vid, $id)
    {
        $user_id = auth()->user()['id'];
        $data = request()->try(['name', 'username', 'password', 'url', 'notes']);

        if (empty($data)) {
            return response()->json(['error' => 'No data provided'], 400);
        }

        $vault = db()->select('vaults')->where(['id' => $vid])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $login = db()
            ->select('logins')
            ->where([
                'id' => $id,
                'vault_id' => $vid
            ])
            ->first();

        if (!$login) {
            return response()->json(['error' => 'Login not found'], 404);
        }

        db()
            ->update('logins')
            ->params($data)
            ->where([
                'id' => $id,
                'vault_id' => $vid
            ])
            ->execute();

        $login = db()
            ->select('logins')
            ->where([
                'id' => $id,
                'vault_id' => $vid
            ])
            ->first();

        return response()->json($login);
    }

    public function destroy($vid, $id)
    {
        $user_id = auth()->user()['id'];

        $vault = db()->select('vaults')->where(['id' => $vid])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $login = db()
            ->select('logins')
            ->where([
                'id' => $id,
                'vault_id' => $vid
            ])
            ->first();

        if (!$login) {
            return response()->json(['error' => 'Login not found'], 404);
        }

        db()
            ->delete('logins')
            ->where([
                'id' => $id,
                'vault_id' => $vid
            ])
            ->execute();

        return response()->json(['message' => 'Login deleted']);
    }
}

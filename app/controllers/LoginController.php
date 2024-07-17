<?php

namespace App\Controllers;

use App\Utils\SymmetricEncryption;
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

        $logins = array_map(function($login) {
            $crypt = new SymmetricEncryption();
            $login['username'] = $crypt->decryptString($login['username']);
            $login['password'] = $crypt->decryptString($login['password']);
            $login['url'] = $crypt->decryptString($login['url']);
            $login['totp'] = $crypt->decryptString($login['totp']);
            $login['notes'] = $crypt->decryptString($login['notes']);
            return $login;
        }, $logins);

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

        $crypt = new SymmetricEncryption();
        $login['username'] = $crypt->decryptString($login['username']);
        $login['password'] = $crypt->decryptString($login['password']);
        $login['url'] = $crypt->decryptString($login['url']);
        $login['totp'] = $crypt->decryptString($login['totp']);
        $login['notes'] = $crypt->decryptString($login['notes']);

        return response()->json($login);
    }

    public function store($vid)
    {
        $data = request()->try(['name', 'username', 'password', 'url', 'notes', 'totp']);

        if (empty($data)) {
            return response()->json(['error' => 'No data provided'], 400);
        }

        $vault = db()->select('vaults')->where(['id' => $vid])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $crypt = new SymmetricEncryption();
        $login = [
            'name' => $data['name'],
            'username' => $crypt->encryptString($data['username']),
            'password' => $crypt->encryptString($data['password']),
            'url' => $crypt->encryptString($data['url']),
            'notes' => $crypt->encryptString($data['notes']),
            'totp' => $crypt->encryptString($data['totp'])
        ];

        $login['vault_id'] = $vid;

        db()->insert('logins')->params([
            'name' => $login['name'],
            'username' => $login['username'],
            'password' => $login['password'],
            'url' => $login['url'],
            'notes' => $login['notes'],
            'vault_id' => $login['vault_id'],
            'totp' => $login['totp']
        ])->execute();


        $login_saved = db()->select('logins')->where(['id' => db()->lastInsertId()])->first();

        if (!$login_saved) {
            return response()->json(['error' => 'An error occurred'], 400);
        }

        return response()->json($login_saved);
    }

    public function update($vid, $id)
    {
        $data = request()->try(['name', 'username', 'password', 'url', 'notes', 'totp']);

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

        $crypt = new SymmetricEncryption();
        $login = [
            'name' => $data['name'],
            'username' => $crypt->encryptString($data['username']),
            'password' => $crypt->encryptString($data['password']),
            'url' => $crypt->encryptString($data['url']),
            'notes' => $crypt->encryptString($data['notes']),
            'totp' => $crypt->encryptString($data['totp'])
        ];

        db()
            ->update('logins')
            ->set([
                'name' => $login['name'],
                'username' => $login['username'],
                'password' => $login['password'],
                'url' => $login['url'],
                'notes' => $login['notes'],
                'totp' => $login['totp']
            ])
            ->where([
                'id' => $id,
                'vault_id' => $vid
            ])
            ->execute();

        $login_updated = db()->select('logins')->where(['id' => $id])->first();

        if (!$login_updated) {
            return response()->json(['error' => 'An error occurred'], 400);
        }

        return response()->json($login_updated);
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

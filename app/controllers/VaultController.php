<?php

namespace App\Controllers;

use Leaf\Http\Request;

class VaultController extends Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function index()
    {
        $user_id = auth()->user()['id'];
        $vaults = db()
            ->select('vaults')
            ->where(['owner_id' => $user_id])
            ->orWhere('shared_id', '=', $user_id)
            ->orderBy('id')
            ->fetchAll();
        return response()->json($vaults);
    }

    public function show($id)
    {
        $user_id = auth()->user()['id'];

        $vault = db()
            ->select('vaults')
            ->where(['id' => $id, 'owner_id' => $user_id])
            ->orWhere(['id' => $id])
            ->where('shared_id', '=', "$user_id")
            ->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        return response()->json($vault);
    }

    public function store()
    {
        $name = request()->get('name');

        $user_id = auth()->user()['id'];

        $query = db()
            ->insert('vaults')
            ->params([
                'name' => $name,
                'owner_id' => $user_id,
                'shared_id' => null
            ])
            ->execute();

        if (!$query) {
            return response()->json(['error' => 'An error occurred'], 400);
        }

        $vault = db()->select('vaults')->where(['id' => db()->lastInsertId()])->first();

        if (!$vault) {
            return response()->json(['error' => 'An error occurred'], 400);
        }

        return response()->json($vault);
    }

    public function update($id)
    {
        $user_id = auth()->user()['id'];
        $data = request()->try(['name']);

        if (empty($data)) {
            return response()->json(['error' => 'No data to update'], 400);
        }

        db()
            ->update('vaults')
            ->params($data)
            ->where(['id' => $id, 'owner_id' => $user_id])
            ->execute();

        $vault = db()->select('vaults')->where(['id' => $id])->first();

        if (!$vault) {
            return response()->json(['error' => 'An error occurred'], 400);
        }

        return response()->json($vault);
    }

    public function destroy($id)
    {
        $user_id = auth()->user()['id'];

        $vault = db()->select('vaults')->where(['id' => $id, 'owner_id' => $user_id])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        db()->delete('vaults')->where(['id' => $id, 'owner_id' => $user_id])->execute();

        $vault = db()->select('vaults')->where(['id' => $id, 'owner_id' => $user_id])->first();

        if ($vault) {
            return response()->json(['error' => 'Vault not deleted'], 400);
        }

        return response()->json(['message' => 'Vault deleted successfully']);
    }

    public function add_user($id)
    {
        $data = request()->try(['username']);
        $user_id = auth()->user()['id'];

        if (empty($data['username'])) {
            return response()->json(['error' => 'No username provided'], 400);
        }

        $sharing_user_id = db()->select('users')->where(['username' => $data['username']])->first();

        if (!$sharing_user_id) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $vault = db()->select('vaults')->where(['id' => $id, 'owner_id' => $user_id])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $shared_users = $vault['shared_id'] != "" ? explode(',', $vault['shared_id']) ?? [] : [];

        if (in_array($sharing_user_id['id'], $shared_users)) {
            return response()->json(['error' => 'User already has access to this vault'], 400);
        }

        $shared_users[] = $sharing_user_id['id'];

        db()
            ->update('vaults')
            ->params(['shared_id' => implode(',', $shared_users)])
            ->where(['id' => $id, 'owner_id' => $user_id])
            ->execute();

        $vault = db()->select('vaults')->where(['id' => $id, 'owner_id' => $user_id])->first();

        return response()->json($vault);
    }

    public function remove_user($id)
    {
        $data = request()->try(['username']);
        $user_id = auth()->user()['id'];

        if (empty($data['username'])) {
            return response()->json(['error' => 'No username provided'], 400);
        }

        $sharing_user_id = db()->select('users')->where(['username' => $data['username']])->first();

        if (!$sharing_user_id) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $vault = db()->select('vaults')->where(['id' => $id, 'owner_id' => $user_id])->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $shared_users = explode(',', $vault['shared_id']) ?? [];

        if (!in_array($sharing_user_id['id'], $shared_users)) {
            return response()->json(['error' => 'User does not have access to this vault'], 400);
        }

        $shared_users = array_diff($shared_users, [$sharing_user_id['id']]);

        db()
            ->update('vaults')
            ->params(['shared_id' => implode(',', $shared_users)])
            ->where(['id' => $id, 'owner_id' => $user_id])
            ->execute();

        $vault = db()->select('vaults')->where(['id' => $id, 'owner_id' => $user_id])->first();

        return response()->json($vault);
    }

    public function get_user($id)
    {
        $user_id = auth()->user()['id'];

        $vault = db()
            ->select('vaults')
            ->where(['id' => $id, 'owner_id' => $user_id])
            ->orWhere(['id' => $id])
            ->where('shared_id', '=', "$user_id")
            ->first();

        if (!$vault) {
            return response()->json(['error' => 'Vault not found'], 404);
        }

        $users = db()
            ->query("SELECT username, fullname, email, id FROM users WHERE id IN (" . $vault['shared_id'] . ")")
            ->fetchAll();

        return response()->json($users);
    }
}

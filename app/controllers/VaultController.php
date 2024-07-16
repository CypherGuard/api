<?php

namespace App\Controllers;

use Leaf\Http\Request;

class VaultController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->request = new Request;
    }

    public function index()
    {
        $user_id = auth()->user()['id'];
        $vaults = db()->select('vaults')->where(['owner_id' => $user_id])->fetchAll();
        return response()->json($vaults);
    }

    public function show($id)
    {
        $user_id = auth()->user()['id'];
        $vault = db()->select('vaults')->where(['id' => $id, 'owner_id' => $user_id])->first();

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
        $data = request()->try(['name', 'owner_id']);

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
        $user_id = request()->get('user_id');
        $vault = db()->select('vaults')->params(['id' => $id])->first();
        $shared_users = $vault['shared_users'] ?? [];
        $shared_users[] = $user_id;
        $vault = db()->update('vaults', ['shared_users' => $shared_users], ['id' => $id]);
        return response()->json($vault);
    }

}

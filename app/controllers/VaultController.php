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
        $vaults = db()->select('vaults')->fetchAll();
        return response()->json($vaults);
    }

    public function show($id)
    {
        $vault = db()->select('vaults', ['id' => $id]);
        return response()->json($vault);
    }

    public function store()
    {
        $name = request()->get('name');

        $id = auth()->user()["id"];

        $query = db()
            ->insert('vaults')
            ->params([
                'name' => $name,
                'owner_id' => $id,
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
        $data = $this->request->all();
        $vault = db()->update('vaults', $data, ['id' => $id]);
        return response()->json($vault);
    }

    public function destroy($id)
    {
        $vault = db()->delete('vaults', ['id' => $id]);
        return response()->json($vault);
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

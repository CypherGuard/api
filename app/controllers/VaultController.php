<?php

namespace App\Controllers;

use Leaf\Http\Request;

class VaultController extends Controller
{
    public function __construct() {
        parent::__construct();
        db()->autoConnect();
        $this->request = new Request;
    }

    public function index()
    {
        $vaults = db()->select('vaults');
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
        $query = db()
            ->insert('vaults')
            ->params(['name' => $name])
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

}

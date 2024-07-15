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
        $name = $this->request->get('name');
        $vault = db()->insert('vaults', ['name' => $name]);
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

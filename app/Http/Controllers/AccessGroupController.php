<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\AccessGroup;

class AccessGroupController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    public function createAccessGroup(Request $req)
    {
        $accessGroup = new AccessGroup();
        $response['error']['status'] = false;


        $validator = Validator::make($req->all(), [
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Grupo não cadastrado';
            return response()->json($response);
        }

        $accessGroup->description = $req->description;
        $accessGroup->save();

        return response()->json($response);
    }

    public function readAllAccessGroups()
    {
        $response['error']['status'] = false;

        $response['AccessGroups'] = AccessGroup::select('id', 'description')
            ->where('deleted', -1)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($response);
    }

    public function readAccessGroup($id)
    {
        $response['error']['status'] = false;

        $response['AccessGroup'] = AccessGroup::where('deleted', -1)
            ->whereNull('deleted_at')
            ->find($id, ['id', 'description']);

        return response()->json($response);
    }

    public function updateAccessGroup(Request $req, $id)
    {
        $response['error']['status'] = false;

        if (AccessGroup::where('deleted', -1)->whereNull('deleted_at')->find($id)) {

            $accessGroup = AccessGroup::find($id);

            if ($req->description) {
                $accessGroup->description = $req->description;
            }

            $accessGroup->save();
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Grupo não existe';
        }

        return response()->json($response);
    }

    public function deleteAccessGroup($id)
    {
        $response['error']['status'] = false;

        if (AccessGroup::where('deleted', -1)->whereNull('deleted_at')->find($id)) {
            $accessGroup = AccessGroup::find($id);

            $accessGroup->deleted = 0;
            $accessGroup->deleted_at = now();
            $accessGroup->save();

            $response["error"]['messeger']  = 'Grupo de acesso exluido';
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Grupo de acesso não Encontrado';
        }

        return response()->json($response);
    }
}

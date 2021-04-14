<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\ComponentAccess;

class ComponentAccessController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    public function createComponentAccess(Request $req)
    {
        $componentAccess = new ComponentAccess();
        $response['error']['status'] = false;

        $validator = Validator::make($req->all(), [
            'id_component' => 'required',
        ]);

        if ($validator->fails()) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Grupo não cadastrado';
            return response()->json($response);
        }

        foreach ($req->all() as $key => $value) {
            $componentAccess->$key = $value;
        }

        $componentAccess->save();

        return response()->json($response);
    }

    public function readAllComponentAccess()
    {
        $response['error']['status'] = false;

        $response['componentAccess'] = ComponentAccess::where('deleted', -1)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($response);
    }

    public function readComponentAccess($id)
    {
        $response['error']['status'] = false;

        $response['ComponentAccess'] = ComponentAccess::where('deleted', -1)
            ->whereNull('deleted_at')
            ->find($id);

        return response()->json($response);
    }

    public function updateComponent(Request $req, $id)
    {
        $response['error']['status'] = false;

        if (ComponentAccess::where('deleted', -1)->whereNull('deleted_at')->find($id)) {

            $componentAccess = ComponentAccess::find($id);

            foreach ($req->all() as $key => $value) {
                $componentAccess->$key = $value;
            }

            $componentAccess->save();

            $response["ComponentAccess"] =  $componentAccess;
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Grupo não existe';
        }

        return response()->json($response);
    }

    public function deleteComponent($id)
    {
        $response['error']['status'] = false;

        if (ComponentAccess::where('deleted', -1)->whereNull('deleted_at')->find($id)) {
            $componentAccess = ComponentAccess::find($id);

            $componentAccess->deleted = 0;
            $componentAccess->deleted_at = now();
            $componentAccess->save();

            $response["error"]['messeger']  = 'Compomente exluido';
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Compomente não Encontrado';
        }

        return response()->json($response);
    }
}

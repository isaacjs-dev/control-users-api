<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Component;

class ComponentController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    public function createComponent(Request $req)
    {
        $component = new Component();
        $response['error']['status'] = false;

        $validator = Validator::make($req->all(), [
            'type' => 'required',
            'name' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Grupo não cadastrado';
            return response()->json($response);
        }

        $component->type = $req->type;
        $component->name = $req->name;
        $component->description = $req->description;

        $component->save();

        return response()->json($response);
    }

    public function readAllComponents()
    {
        $response['error']['status'] = false;

        $response['components'] = Component::where('deleted', -1)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($response);
    }

    public function readComponent($id)
    {
        $response['error']['status'] = false;

        $response['component'] = Component::where('deleted', -1)
            ->whereNull('deleted_at')
            ->find($id);

        return response()->json($response);
    }

    public function updateComponent(Request $req, $id)
    {
        $response['error']['status'] = false;

        if (Component::where('deleted', -1)->whereNull('deleted_at')->find($id)) {

            $component = Component::find($id);

            foreach ($req->all() as $key => $value) {
                $component->$key = $value;
            }

            $component->save();

            $response["component"] =  $component;
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Grupo não existe';
        }

        return response()->json($response);
    }

    public function deleteComponent($id)
    {
        $response['error']['status'] = false;

        if (Component::where('deleted', -1)->whereNull('deleted_at')->find($id)) {
            $component = Component::find($id);

            $component->deleted = 0;
            $component->deleted_at = now();
            $component->save();

            $response["error"]['messeger']  = 'Compomente exluido';
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Compomente não Encontrado';
        }

        return response()->json($response);
    }
}

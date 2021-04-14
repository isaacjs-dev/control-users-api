<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use SebastianBergmann\Diff\Diff;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'unauthorized']]);
    }

    public function login(Request $req)
    {
        $response['error']['status'] = false;

        $token = Auth::attempt([
            'email' => $req->email,
            'password' => $req->password
        ]);

        if (!$token) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Usuário e/ou senha errados!';

            return response()->json($response);
        }

        $loggedUser  = Auth::user();

        // Permições de Grupo -----------
        $authorizedRoute = (User::select('components.id', 'components.route')
            ->leftJoin("access_groups", "users.id_access_groups", "=", "access_groups.id")
            ->leftJoin("component_access", "access_groups.id", "=", "component_access.id_access_group")
            ->leftJoin("components", "component_access.id_component", "=", "components.id")

            ->where('users.id', $loggedUser->id)

            ->where('components.type', 1)
            ->whereNotNull('components.route')
            ->where('components.deleted', -1)
            ->whereNull('components.deleted_at')

            ->where('component_access.permission', 1)
            ->where('component_access.deleted', -1)
            ->whereNull('component_access.deleted_at')

            ->where('access_groups.deleted', -1)
            ->whereNull('access_groups.deleted_at')

            ->where('users.deleted', -1)
            ->whereNull('users.deleted_at')
            ->get())->toArray();

        $authorizedMenuGroup = (User::select('components.id', 'components.description')
            ->leftJoin("access_groups", "users.id_access_groups", "=", "access_groups.id")
            ->leftJoin("component_access", "access_groups.id", "=", "component_access.id_access_group")
            ->leftJoin("components", "component_access.id_component", "=", "components.id")

            ->where('users.id', $loggedUser->id)

            ->where('components.type', 3)
            ->whereNull('components.route')
            ->where('components.deleted', -1)
            ->whereNull('components.deleted_at')

            ->where('component_access.permission', 1)
            ->where('component_access.deleted', -1)
            ->whereNull('component_access.deleted_at')

            ->where('access_groups.deleted', -1)
            ->whereNull('access_groups.deleted_at')

            ->where('users.deleted', -1)
            ->whereNull('users.deleted_at')
            ->get())->toArray();

        $authorizedComponent = (User::select('components.id',)
            ->leftJoin("access_groups", "users.id_access_groups", "=", "access_groups.id")
            ->leftJoin("component_access", "access_groups.id", "=", "component_access.id_access_group")
            ->leftJoin("components", "component_access.id_component", "=", "components.id")

            ->where('users.id', $loggedUser->id)

            ->where('components.type', 2)
            ->whereNull('components.route')
            ->where('components.deleted', -1)
            ->whereNull('components.deleted_at')

            ->where('component_access.permission', 1)
            ->where('component_access.deleted', -1)
            ->whereNull('component_access.deleted_at')

            ->where('access_groups.deleted', -1)
            ->whereNull('access_groups.deleted_at')

            ->where('users.deleted', -1)
            ->whereNull('users.deleted_at')
            ->get())->toArray();


        // /Permições de Grupo ----------

        // Permições individuais --------
        $authorizedRoute =
            [
                ...$authorizedRoute,
                ...(User::select('components.id', 'components.route')
                    ->leftJoin("component_access", "users.id", "=", "component_access.id_user")
                    ->leftJoin("components", "component_access.id_component", "=", "components.id")

                    ->where('users.id', $loggedUser->id)

                    ->where('components.type', 1)
                    ->whereNotNull('components.route')
                    ->where('components.deleted', -1)
                    ->whereNull('components.deleted_at')

                    ->where('component_access.permission', 1)
                    ->where('component_access.deleted', -1)
                    ->whereNull('component_access.deleted_at')

                    ->where('users.deleted', -1)
                    ->whereNull('users.deleted_at')
                    ->get())->toArray()
            ];

        $authorizedMenuGroup =
            [
                ...$authorizedMenuGroup,
                ...(User::select('components.id', 'components.description')
                    ->leftJoin("component_access", "users.id", "=", "component_access.id_user")
                    ->leftJoin("components", "component_access.id_component", "=", "components.id")

                    ->where('users.id', $loggedUser->id)

                    ->where('components.type', 3)
                    ->whereNull('components.route')
                    ->where('components.deleted', -1)
                    ->whereNull('components.deleted_at')

                    ->where('component_access.permission', 1)
                    ->where('component_access.deleted', -1)
                    ->whereNull('component_access.deleted_at')

                    ->where('users.deleted', -1)
                    ->whereNull('users.deleted_at')
                    ->get())->toArray()
            ];

        $authorizedComponent =
            [
                ...$authorizedComponent,
                ...(User::select('components.id')
                    ->leftJoin("component_access", "users.id", "=", "component_access.id_user")
                    ->leftJoin("components", "component_access.id_component", "=", "components.id")

                    ->where('users.id', $loggedUser->id)

                    ->where('components.type', 2)
                    ->whereNull('components.route')
                    ->where('components.deleted', -1)
                    ->whereNull('components.deleted_at')

                    ->where('component_access.permission', 1)
                    ->where('component_access.deleted', -1)
                    ->whereNull('component_access.deleted_at')

                    ->where('users.deleted', -1)
                    ->whereNull('users.deleted_at')
                    ->get())->toArray()
            ];
        // / Permições individuais ------

        // Permições Negadas ------------

        $unauthorizedData = (User::select('components.id')
            ->leftJoin("component_access", "users.id", "=", "component_access.id_user")
            ->leftJoin("components", "component_access.id_component", "=", "components.id")

            ->where('users.id', $loggedUser->id)
            ->where('component_access.permission', -1)
            ->get())->toArray();


        foreach ($unauthorizedData as $values) {
            $unauthorized[] = $values['id'];
        }

        $responseAuthorized["route"] = [];
        $responseAuthorized["menuGroup"] = [];
        $responseAuthorized["component"] = [];

        $idAuthorizedAdd = [];
        foreach ($authorizedRoute as $Route) {
            if (!in_array($Route['id'], $unauthorized) && !in_array($Route['id'], $idAuthorizedAdd)) {
                $responseAuthorized["route"][] = $Route;
                $idAuthorizedAdd[] = $Route['id'];
            }
        }
        $idAuthorizedAdd = [];
        foreach ($authorizedMenuGroup as $MenuGroup) {
            if (!in_array($MenuGroup['id'], $unauthorized)  && !in_array($Route['id'], $idAuthorizedAdd)) {
                $responseAuthorized["menuGroup"][] = $MenuGroup;
                $idAuthorizedAdd[] = $Route['id'];
            }
        }
        $idAuthorizedAdd = [];
        foreach ($authorizedComponent as $Component) {
            if (!in_array($Component['id'], $unauthorized)  && !in_array($Route['id'], $idAuthorizedAdd)) {
                $responseAuthorized["component"][] = $Component;
                $idAuthorizedAdd[] = $Route['id'];
            }
        }





        $response["user"] = $loggedUser->toArray();
        $response["user"]['authorized'] = $responseAuthorized;
        $response["token"] = $token;

        return response()->json($response);
    }

    public function logout()
    {
        Auth::logout();

        $response['error']['status'] = false;
        return response()->json($response);
    }

    public function refresh()
    {
        $response['error']['status'] = false;

        $response["user"]['token'] = Auth::refresh();
        $response["user"]['data'] = Auth::user();

        return response()->json($response);
    }

    public function unauthorized()
    {
        $response["error"]['status']  = true;
        $response["error"]['messeger']  = 'Não autorizado';

        return response()->json($response, 401);
    }
}

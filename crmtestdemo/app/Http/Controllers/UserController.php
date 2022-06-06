<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Rol;
use App\Models\Empresa;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $roles = Rol::all();
        $empresas = Empresa::all();

        return view('users.index', compact('users', 'roles', 'empresas'));
    }

    public function show(User $user)
    {
        $userJson = $user->toArray();
        unset($userJson['created_at']);
        unset($userJson['updated_at']);
        unset($userJson['empresa_id']);
        unset($userJson['rol_id']);
        $userJson['empresa'] = $user->empresa->nombre;
        $userJson['rol'] = $user->rol->nombre;
        return response()->json($userJson);
    }

    public function create()
    {
        return "Crear nuevo usuario";
    }

    public function edit(User $user)
    {
        return response()->json($user->toArray());
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'nombre' => 'required',
            'cedula' => 'required',
            'email1' => 'required|email|unique:users,email1',
            'email2' => 'required|email|unique:users,email2',
            'direccion' => 'required',
            'empresa_id' => 'required',
            //'password' => 'required',
            'rol_id' => 'required',
        ]);

        $data['password'] = bcrypt('dummy');
        User::create($data);
        return redirect()->route('users.index');
    }

    public function update(User $user)
    {
        $email1 = request()->input('email1');
        $email2 = request()->input('email2');
        $data = request()->validate([
            'nombre' => 'required',
            'cedula' => 'required',
            'email1' => [
                'required', 'email',
                Rule::unique('users', 'email1')->ignore($user),
                Rule::unique('users', 'email2'),
            ],
            'email2' => [
                'required', 'email',
                Rule::unique('users', 'email1'),
                Rule::unique('users', 'email2')->ignore($user),
            ],
            'direccion' => 'required',
            'empresa_id' => 'required',
            //'password' => 'required',
            'rol_id' => 'required',
        ]);

        $user->update($data);

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}

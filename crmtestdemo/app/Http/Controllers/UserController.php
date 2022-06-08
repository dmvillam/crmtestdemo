<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Rol;
use App\Models\Empresa;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $users = User::all();
        $roles = Rol::all();
        $empresas = Empresa::all();

        return view('users.index', compact('users', 'roles', 'empresas'));
    }

    public function show(User $user)
    {
        $userJson = $user->select('nombre', 'cedula', 'email1', 'email2', 'direccion')->findOrFail($user->id)->toArray();
        $userJson['empresa'] = $user->empresa ? $user->empresa->nombre : 'Ninguna';
        $userJson['rol'] = $user->rol->nombre;
        return response()->json($userJson);
    }

    public function edit(User $user)
    {
        return response()->json($user->toArray());
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'nombre' => 'required',
            'cedula' => 'required|numeric',
            'email1' => [
                'required', 'email', 'different:email2',
                Rule::unique('users', 'email1'),
                Rule::unique('users', 'email2'),
            ],
            'email2' => [
                'required', 'email', 'different:email1',
                Rule::unique('users', 'email1'),
                Rule::unique('users', 'email2'),
            ],
            'direccion' => 'required',
            'empresa_id' => 'required',
            'password' => 'required',
            'rol_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.index')
                ->withErrors($validator, 'store')
                ->withInput();
        }

        $data = $validator->validated();
        
        $data['password'] = bcrypt($data['password']);
        User::create($data);
        return redirect()->route('users.index')->with('status', '¡Usuario creado de manera exitosa!');
    }

    public function update(User $user)
    {
        $email1 = request()->input('email1');
        $email2 = request()->input('email2');
        $validator = Validator::make(request()->all(), [
            'nombre' => 'required',
            'cedula' => 'required|numeric',
            'email1' => [
                'required', 'email', 'different:email2',
                Rule::unique('users', 'email1')->ignore($user),
                Rule::unique('users', 'email2')->ignore($user),
            ],
            'email2' => [
                'required', 'email', 'different:email1',
                Rule::unique('users', 'email1')->ignore($user),
                Rule::unique('users', 'email2')->ignore($user),
            ],
            'direccion' => 'required',
            'empresa_id' => 'required',
            'password' => '',
            'rol_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('users.index')
                ->withErrors($validator, 'update')
                ->withInput();
        }

        $data = $validator->validated();

        if ($data['password'] != null) {
            $data['password'] = bcrypt($data['password']);
        } else unset($data['password']);
        $user->update($data);

        return redirect()->route('users.index')->with('status', '¡Usuario actualizado de manera exitosa!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('status', '¡Usuario borrado de manera exitosa!');
    }
}

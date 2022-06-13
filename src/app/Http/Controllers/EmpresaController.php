<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Models\Empresa;

class EmpresaController extends Controller
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
        $empresas = Empresa::all();
        return view('companies.index', compact('empresas'));
    }

    public function show(Empresa $empresa)
    {
        return response()->json([
            'company' => $empresa->nombre,
            'html' => view('companies.partials.details', compact('empresa'))->render(),
        ]);
    }

    public function edit(Empresa $empresa)
    {
        return response()->json($empresa->toArray());
    }

    private function handleLogo(Empresa $empresa)
    {
        if(request()->file('logo')){
            $file = request()->file('logo');
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('img/logos'), $filename);
            $empresa['logo'] = $filename;
            $empresa->save();
        }
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'cedula_juridica'   => 'required',
            'nombre'            => 'required',
            'telefono'          => 'required',
            'email'             => 'required|email|unique:empresas,email',
            'logo'              => '',
            'direccion'         => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('companies.index')
                ->withErrors($validator, 'store')
                ->withInput();
        }

        $data = $validator->validated();
        
        $empresa = Empresa::create($data);
        $this->handleLogo($empresa);

        return redirect()->route('companies.index')->with('status', "¡Empresa *{$empresa->nombre}* creada de manera exitosa!");
    }

    public function update(Empresa $empresa)
    {
        $validator = Validator::make(request()->all(), [
            'cedula_juridica'   => 'required',
            'nombre'            => 'required',
            'telefono'          => 'required',
            'email'             => ['required', 'email', Rule::unique('empresas', 'email')->ignore($empresa)],
            'logo'              => '',
            'direccion'         => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('companies.index')
                ->withErrors($validator, 'update')
                ->withInput();
        }

        $data = $validator->validated();
        $empresa->update($data);

        // Handle the logo file
        $logo = public_path('img/logos')."/{$empresa->logo}";
        if ($empresa->logo && file_exists($logo)) {
            unlink($logo);
        }
        $this->handleLogo($empresa);

        return redirect()->route('companies.index')->with('status', "¡Empresa *{$empresa->nombre}* actualizada de manera exitosa!");
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return redirect()->route('companies.index')->with('status', "¡Empresa *{$empresa->nombre}* eliminada de manera exitosa!");
    }
}

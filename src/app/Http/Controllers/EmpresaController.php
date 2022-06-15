<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Image;
use Illuminate\Support\Facades\Storage;

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

    private function _handleLogo(Empresa $empresa, $action='store')
    {
        if(request()->file('logo'))
        {
            if ($action=='update') {
                if (Storage::disk('logos')->exists($empresa->logo))
                    Storage::disk('logos')->delete($empresa->logo);
            }

            $path = request()->file('logo')->store('/', 'logos');
            $empresa['logo'] = $path;
            $empresa->save();
        }
    }

    private function handleLogo(Empresa $empresa, $action='store')
    {
        if (request()->file('logo'))
        {
            if ($action=='update') {
                if (Storage::disk('logos')->exists($empresa->logo))
                    Storage::disk('logos')->delete($empresa->logo);
            }

            $logo = request()->file('logo');
            $logoname = $logo->hashName();
         
            $path = storage_path('app/public/logos');
            $img = Image::make($logo->path());
            if ($img->width() > 500 || $img->height() > 500) {
                $img->resize(500, 500, function ($const) {
                    $const->aspectRatio();
                });
            }

            Storage::disk('logos')->put($logoname, (string) $img->encode());

            $empresa['logo'] = $logoname;
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
            'logo'              => 'image|mimes:jpg,jpeg,png,svg,gif|max:2048',
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
            'logo'              => 'image|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'direccion'         => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('companies.index')
                ->withErrors($validator, 'update')
                ->withInput();
        }

        $data = $validator->validated();
        $empresa->update($data);
        $this->handleLogo($empresa, 'update');


        return redirect()->route('companies.index')->with('status', "¡Empresa *{$empresa->nombre}* actualizada de manera exitosa!");
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        if (Storage::disk('logos')->exists($empresa->logo))
            Storage::disk('logos')->delete($empresa->logo);
        
        return redirect()->route('companies.index')->with('status', "¡Empresa *{$empresa->nombre}* eliminada de manera exitosa!");
    }
}

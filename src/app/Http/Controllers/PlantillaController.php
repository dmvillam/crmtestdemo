<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Plantilla;

class PlantillaController extends Controller
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
        $plantillas = Plantilla::all();
        return view('templates.index', compact('plantillas'));
    }

    public function show(Plantilla $plantilla)
    {
        return response()->json([
            'template' => $plantilla->nombre,
            'html' => view('templates.partials.details', compact('plantilla'))->render(),
        ]);
    }

    public function edit(Plantilla $plantilla)
    {
        return response()->json($plantilla->toArray());
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'nombre'            => 'required',
            'descripcion_larga' => 'required',
            'descripcion_corta' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('templates.index')
                ->withErrors($validator, 'store')
                ->withInput();
        }

        $data = $validator->validated();
        $Plantilla = plantilla::create($data);

        return redirect()->route('templates.index')->with('status', "¡Plantilla *{$plantilla->nombre}* creada de manera exitosa!");
    }

    public function update(Plantilla $plantilla)
    {
        $validator = Validator::make(request()->all(), [
            'nombre'            => 'required',
            'descripcion_larga' => 'required',
            'descripcion_corta' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('templates.index')
                ->withErrors($validator, 'update')
                ->withInput();
        }

        $data = $validator->validated();
        $plantilla->update($data);

        return redirect()->route('templates.index')->with('status', "¡Plantilla *{$plantilla->nombre}* actualizada de manera exitosa!");
    }

    public function destroy(Plantilla $plantilla)
    {
        $plantilla->delete();
        
        return redirect()->route('templates.index')->with('status', "¡Plantilla *{$plantilla->nombre}* eliminada de manera exitosa!");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tarea;

class TaskController extends Controller
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
        $tareas = Tarea::all();
        $tipos_mantenimiento = ['P1', 'P2', 'P3', 'P4', 'P5'];
        return view('tasks.index', compact('tareas', 'tipos_mantenimiento'));
    }

    public function show(Tarea $tarea)
    {
        return response()->json([
            'task' => $tarea->nombre,
            'html' => view('tasks.partials.details', compact('tarea'))->render(),
        ]);
    }

    public function edit(Tarea $tarea)
    {
        return response()->json($tarea->toArray());
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

        return redirect()->route('companies.index')->with('status', "¡Tarea *{$tarea->nombre}* creada de manera exitosa!");
    }

    public function update(Tarea $tarea)
    {
        $validator = Validator::make(request()->all(), [
            'cedula_juridica'   => 'required',
            'nombre'            => 'required',
            'telefono'          => 'required',
            'email'             => ['required', 'email', Rule::unique('empresas', 'email')->ignore($tarea)],
            'logo'              => 'image|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'direccion'         => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('companies.index')
                ->withErrors($validator, 'update')
                ->withInput();
        }

        $data = $validator->validated();
        $tarea->update($data);
        $this->handleLogo($tarea, 'update');


        return redirect()->route('companies.index')->with('status', "¡Tarea *{$tarea->nombre}* actualizada de manera exitosa!");
    }

    public function destroy(Tarea $tarea)
    {
        $tarea->delete();

        if (Storage::disk('logos')->exists($tarea->logo))
            Storage::disk('logos')->delete($tarea->logo);
        
        return redirect()->route('companies.index')->with('status', "¡Tarea *{$tarea->nombre}* eliminada de manera exitosa!");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Plantilla;

class PlantillaController extends Controller
{
    private $tinymce_key = "4r1iu4omrhlz3v91a6ho8qokiuqb49x2v8o92gegp8gf5arb";

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
        $tinymce_key = $this->tinymce_key;
        return view('templates.index', compact('plantillas', 'tinymce_key'));
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

    public function upload()
    {
        if (request()->file('fileimage')) {
            $path = request()->file('fileimage')->store('/', 'uploads');
            return response()->json(["/storage/uploads/$path"]); 
        }
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
        $plantilla = plantilla::create($data);

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

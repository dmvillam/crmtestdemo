<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Rol;
use App\Models\Tarea;
use App\Models\Notificacion;

class ReportesController extends Controller
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
    
    public function index($id_cliente = null, $estado = null, $rango = null)
    {
        $clientes = Rol::where('nombre', '=', 'Cliente')->first()->users()->orderBy('nombre')->get();

        if ($id_cliente && $id_cliente!=0) {
            $cliente = User::findOrFail($id_cliente);
            $notificaciones = $cliente->notificaciones;
        }
        else $notificaciones = Notificacion::all();

        if ($rango && count(explode(" - ", $rango)) == 2) {
            $from = date('Y-m-d', strtotime(str_replace('-', '/', explode(" - ", $rango)[0]))) . " 00:00:00";
            $to = date('Y-m-d', strtotime(str_replace('-', '/', explode(" - ", $rango)[1]))) . " 23:59:59";
            $notificaciones = $notificaciones->where('next_activity','>',$from)
                ->where('next_activity','<',$to);
        }

        return view('reports.index', compact('notificaciones', 'clientes'));
    }
}

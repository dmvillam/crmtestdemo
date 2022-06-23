<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Models\Rol;
use App\Models\User;
use App\Models\Tarea;
use App\Models\Plantilla;
use App\Models\Notificacion;

use App\Mail\NotificacionMail;

use Carbon\Carbon;

class TareaController extends Controller
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
        $plantillas = Plantilla::all();
        $rol_clientes = Rol::where('nombre', '=', 'Cliente')->first()->id;
        $clientes = User::where('rol_id', '=', $rol_clientes)->orderBy('nombre')->get();
        return view('tasks.index', compact('tareas', 'tipos_mantenimiento', 'plantillas', 'clientes'));
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
        $_tarea = $tarea->toArray();
        $_tarea['notificacion'] = $tarea->notificacion ? 1 : 0;
        if ($tarea->notificacion) {
            $_tarea['notif_nombre'] = $tarea->notificacion->nombre;
            $_tarea['plantilla_id'] = $tarea->notificacion->plantilla_id;
            $_tarea['telefono'] = $tarea->notificacion->telefono;
            $_tarea['email'] = $tarea->notificacion->email;
            $_tarea['notificar_email'] = $tarea->notificacion->notificar_email;
            $_tarea['notificar_sms'] = $tarea->notificacion->notificar_sms;
        }
        return response()->json($_tarea);
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'tipo_mantenimiento'    => 'required',
            'nombre'                => 'required',
            'user_id'               => 'required',
            'periodicidad'          => 'required|numeric|multiple_of:5',
            'notif_nombre'          => 'required_unless:notificacion,null',
            'plantilla_id'          => 'required_unless:notificacion,null',
            'telefono'              => 'required_with_all:notificacion,notificar_sms',
            'email'                 => 'required_with_all:notificacion,notificar_email|email|nullable',
            'notificacion'          => '',
            'notificar_email'       => '',
            'notificar_sms'         => '',
        ]);

        if ($validator->fails()) {
            return redirect()->route('tasks.index')
                ->withErrors($validator, 'store')
                ->withInput();
        }

        $data = $validator->validated();
        if (isset($data['notificacion'])) {
            $tarea = Tarea::create($data);
            $data['nombre'] = $data['notif_nombre'];
            $data['notificar_email'] = isset($data['notificar_email']) ? 1 : 0;
            $data['notificar_sms'] = isset($data['notificar_sms']) ? 1 : 0;
            $data['last_activity'] = date('Y-m-d H:i:s', 0);
            $notificacion = Notificacion::create($data);
            $tarea->notificacion_id = $notificacion->id;
            $tarea->save();
        } else {
            $tarea = Tarea::create($data);
        }

        return redirect()->route('tasks.index')->with('status', "¡Tarea *{$tarea->nombre}* creada de manera exitosa!");
    }

    public function update(Tarea $tarea)
    {
        $validator = Validator::make(request()->all(), [
            'tipo_mantenimiento'    => 'required',
            'nombre'                => 'required',
            'user_id'               => 'required',
            'periodicidad'          => 'required|numeric|multiple_of:5',
            'notif_nombre'          => 'required_unless:notificacion,null',
            'plantilla_id'          => 'required_unless:notificacion,null',
            'telefono'              => 'required_with_all:notificacion,notificar_sms',
            'email'                 => 'required_with_all:notificacion,notificar_email|email|nullable',
            'notificacion'          => '',
            'notificar_email'       => '',
            'notificar_sms'         => '',
        ]);

        if ($validator->fails()) {
            return redirect()->route('tasks.index')
                ->withErrors($validator, 'update')
                ->withInput();
        }

        $data = $validator->validated();
        $tarea->update($data);
        if (isset($data['notificacion'])) {
            $data['nombre'] = $data['notif_nombre'];
            $data['notificar_email'] = isset($data['notificar_email']) ? 1 : 0;
            $data['notificar_sms'] = isset($data['notificar_sms']) ? 1 : 0;
            if ($tarea->notificacion) {
                $tarea->notificacion->update($data);
            } else {
                $data['last_activity'] = date('Y-m-d H:i:s', 0);
                $notificacion = Notificacion::create($data);
                $tarea->notificacion_id = $notificacion->id;
                $tarea->save();
            }
        } else {
            if ($tarea->notificacion) {
                $tarea->notificacion->delete();
                $tarea->notificacion_id = null;
                $tarea->save();
            }
        }

        return redirect()->route('tasks.index')->with('status', "¡Tarea *{$tarea->nombre}* actualizada de manera exitosa!");
    }

    public function destroy(Tarea $tarea)
    {
        if ($tarea->notificacion)
            $tarea->notificacion->delete();
        $tarea->delete();
        
        return redirect()->route('tasks.index')->with('status', "¡Tarea *{$tarea->nombre}* eliminada de manera exitosa!");
    }

    private function addMinutesToDate($minutes, $date)
    {
        return date('Y-m-d H:i:s', strtotime($date) + $minutes*60);
    }

    public function cron()
    {
        $output = "";
        foreach(Notificacion::all() as $notificacion)
        {
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $next_time = $this->addMinutesToDate($notificacion->tarea->periodicidad, $notificacion->last_activity);
            if ($now >= $next_time) {
                $notificacion->last_activity = $now;
                $notificacion->next_activity = $this->addMinutesToDate($notificacion->tarea->periodicidad, $now);
                $notificacion->save();

                if ($notificacion->notificar_email && $notificacion->email && $notificacion->plantilla && $notificacion->plantilla->descripcion_larga)
                {
                    // Enviar mail
                    Mail::to($notificacion->email)->send(new NotificacionMail($notificacion->plantilla));
                    $output .= "Enviando email a <em>{$notificacion->email}...</em><br/>";
                }

                if ($notificacion->notificar_sms && $notificacion->telefono && $notificacion->plantilla && $notificacion->plantilla->descripcion_corta)
                {
                    // Enviar SMS
                    $output .= "Enviando SMS a <em>{$notificacion->telefono}...</em><br/>";
                }
            }
        }

        return $output;
    }
}

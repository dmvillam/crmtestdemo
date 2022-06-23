<div class="row justify-content-start">
	<div class="col-6"><strong>Tipo de mantenimiento:</strong></div> <div class="col-6">{{$tarea->tipo_mantenimiento}}</div>
</div>
<div class="row justify-content-start">
	<div class="col-6"><strong>Nombre de la tarea:</strong></div> <div class="col-6">{{$tarea->nombre}}</div>
</div>
<div class="row justify-content-start">
	<div class="col-6"><strong>Peridiocidad:</strong></div> <div class="col-6">Cada {{$tarea->periodicidad}} min</div>
</div>
<div class="row justify-content-start">
	<div class="col-6"><strong>Cliente:</strong></div> <div class="col-4">{{$tarea->cliente->nombre}}</div>
</div>
<div class="row justify-content-start">
	<div class="col-6"><strong>Notificación:</strong></div> <div class="col-6">{{$tarea->notificacion ? $tarea->notificacion->nombre : "Sin notificación"}}</div>
</div>

@if ($tarea->notificacion)
	<div class="row justify-content-start">
		<div class="col-12"><strong>Notificación por email:</strong></div>
		<div class="col-12 m-3">
			@if ($tarea->notificacion->notificar_email)
			{!! $tarea->notificacion->plantilla->descripcion_larga !!}
			@else
			<em>Notificación Email inactiva</em>
			@endif
		</div>
	</div>
	<div class="row justify-content-start">
		<div class="col-6"><strong>Notificación por SMS:</strong></div>
		<div class="col-6">
			@if ($tarea->notificacion->notificar_sms)
			{!! $tarea->notificacion->plantilla->descripcion_corta !!}
			@else
			<em>Notificación SMS inactiva</em>
			@endif
		</div>
	</div>
@endif

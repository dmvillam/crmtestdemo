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
	<div class="col-6"><strong>Incluye notificación:</strong></div> <div class="col-6">{{$tarea->notificacion ? "Sí" : "No"}}</div>
</div>
<div class="row justify-content-start">
	<div class="col-6"><strong>Notificación por SMS:</strong></div> <div class="col-6">{{$tarea->notificacion ? ($tarea->notificacion->notificar_email?"Sí":"No") : "No"}}</div>
</div>
<div class="row justify-content-start">
	<div class="col-6"><strong>Notificación por email:</strong></div> <div class="col-6">{{$tarea->notificacion ? ($tarea->notificacion->notificar_sms?"Sí":"No") : "No"}}</div>
</div>
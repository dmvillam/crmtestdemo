@if ($empresa->logo)
<div class="company-logo">
	@if (preg_match("/^https?\:\/\//", $empresa->logo))
	<img src="{{$empresa->logo}}" alt="Logo de {{$empresa->nombre}}" title="Logo de {{$empresa->nombre}}">
	@else
	<img src="{{ asset('img/logos') . '/' . $empresa->logo }}" alt="Logo de {{$empresa->nombre}}" title="Logo de {{$empresa->nombre}}">
	@endif
</div>
@else
<div><em>Sin logo</em></div>
@endif
<br>
<div class="row justify-content-start"><div class="col-4"><strong>Cédula jurídica:</strong></div> <div class="col-4">{{$empresa->cedula_juridica}}</div></div>
<div class="row justify-content-start"><div class="col-4"><strong>Nombre:</strong></div> <div class="col-4">{{$empresa->nombre}}</div></div>
<div class="row justify-content-start"><div class="col-4"><strong>Correo:</strong></div> <div class="col-4">{{$empresa->email}}</div></div>
<div class="row justify-content-start"><div class="col-4"><strong>Teléfono:</strong></div> <div class="col-4">{{$empresa->telefono}}</div></div>
<div class="row justify-content-start"><div class="col-4"><strong>Dirección:</strong></div> <div class="col-4">{{$empresa->direccion}}</div></div>
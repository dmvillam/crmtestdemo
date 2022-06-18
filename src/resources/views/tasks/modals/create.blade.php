<div class="modal fade" id="modal-crear-tarea" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crear Tarea</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	@if ($errors->store->any())
      	<h6 class="alert alert-danger">Se han producido los siguientes errores:</h6>
      	@endif
        <form id="form-crear-tarea" action="{{ route('tasks.store') }}" method="POST">
          {{ csrf_field() }}
          <div class="mb-3">
            <label for="tipo_mantenimiento" class="col-form-label">Tipo de mantenimiento <span class="text-danger">*</span></label>
            <select class="form-control @error('tipo_mantenimiento', 'store') is-invalid @enderror" id="tipo_mantenimiento" name="tipo_mantenimiento">
              <option value="">Elija un tipo</option>
              @foreach ($tipos_mantenimiento as $tipo)
                <option value="{{ $tipo }}" {{ (old('tipo_mantenimiento') == $tipo ? 'selected':'') }}>{{ $tipo }}</option>
              @endforeach
            </select>
          </div>
          @error('tipo_mantenimiento', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="nombre" class="col-form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre', 'store') is-invalid @enderror" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}">
          </div>
          @error('nombre', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="user_id" class="col-form-label">Cliente <span class="text-danger">*</span></label>
            <select class="form-control @error('user_id', 'store') is-invalid @enderror" id="user_id" name="user_id">
              <option value="">Elija una cliente</option>
              @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}" {{ (old('user_id') == $cliente->id ? 'selected':'') }}>{{ $cliente->nombre }}</option>
              @endforeach
            </select>
          </div>
          @error('user_id', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="periodicidad" class="col-form-label">Periodicidad (min)<span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('periodicidad', 'store') is-invalid @enderror" id="periodicidad" name="periodicidad" placeholder="Periodicidad" value="{{ old('periodicidad') }}" min="5" step="5">
          </div>
          @error('periodicidad', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label class="col-form-label noselect">
              <input type="checkbox" id="notificacion" name="notificacion" @if(old('notificacion')) checked @endif>
              Crear notificación
            </label>
          </div>
          <div class="form-notificacion">
            <hr>
            <div class="mb-3">
              <label for="notif_nombre" class="col-form-label">Nombre de la Notificación <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('notif_nombre', 'store') is-invalid @enderror" id="notif_nombre" name="notif_nombre" placeholder="Nombre" value="{{ old('notif_nombre') }}" required>
            </div>
            @error('notif_nombre', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="mb-3">
              <label for="plantilla_id" class="col-form-label">Plantilla para el correo/sms <span class="text-danger">*</span></label>
              <select class="form-control @error('plantilla_id', 'store') is-invalid @enderror" id="plantilla_id" name="plantilla_id">
                <option value="">Elija una plantilla</option>
                @foreach ($plantillas as $plantilla)
                  <option value="{{ $plantilla->id }}" {{ (old('plantilla_id') == $plantilla->id ? 'selected':'') }}>{{ $plantilla->nombre }}</option>
                @endforeach
              </select>
            </div>
            @error('plantilla_id', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="mb-3">
              <label class="col-form-label noselect"><input type="checkbox" id="notificar_sms" name="notificar_sms" @if(old('notificar_sms')) checked @endif value="1"> Teléfono</label>
              <input type="text" class="form-control @error('telefono', 'store') is-invalid @enderror" id="telefono" name="telefono" placeholder="Teléfono al cual enviar SMS" value="{{ old('telefono') }}">
            </div>
            @error('telefono', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="mb-3">
              <label class="col-form-label noselect"><input type="checkbox" id="notificar_email" name="notificar_email" @if(old('notificar_email')) checked @endif value="1"> Correo</label>
              <input type="email" class="form-control @error('email', 'store') is-invalid @enderror" id="email" name="email" placeholder="Correo al cual notificar" value="{{ old('email') }}">
            </div>
            @error('email', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-crear-tarea">Crear nueva tarea</button>
      </div>
    </div>
  </div>
</div>

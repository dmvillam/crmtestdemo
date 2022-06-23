<div class="modal fade" id="modal-editar-tarea" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if ($errors->update->any())
        <h6 class="alert alert-danger">Se han producido los siguientes errores:</h6>
        @endif
        <form id="form-editar-tarea" action="" method="POST">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="PUT"/>
          <input type="hidden" name="id" id="edit_id" value="{{old('id')}}">

          <div class="mb-3">
            <label for="tipo_mantenimiento" class="col-form-label">Tipo de mantenimiento <span class="text-danger">*</span></label>
            <select class="form-control @error('tipo_mantenimiento', 'update') is-invalid @enderror" id="edit_tipo_mantenimiento" name="tipo_mantenimiento">
              <option value="">Elija un tipo</option>
              @foreach ($tipos_mantenimiento as $tipo)
                <option value="{{ $tipo }}" {{ (old('tipo_mantenimiento') == $tipo ? 'selected':'') }}>{{ $tipo }}</option>
              @endforeach
            </select>
          </div>
          @error('tipo_mantenimiento', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="nombre" class="col-form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre', 'update') is-invalid @enderror" id="edit_nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}">
          </div>
          @error('nombre', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="user_id" class="col-form-label">Cliente <span class="text-danger">*</span></label>
            <select class="form-control @error('user_id', 'update') is-invalid @enderror" id="edit_user_id" name="user_id">
              <option value="">Elija una cliente</option>
              @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}" {{ (old('user_id') == $cliente->id ? 'selected':'') }}>{{ $cliente->nombre }}</option>
              @endforeach
            </select>
          </div>
          @error('user_id', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="periodicidad" class="col-form-label">Periodicidad (min)<span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('periodicidad', 'update') is-invalid @enderror" id="edit_periodicidad" name="periodicidad" placeholder="Periodicidad (En múltiplos de 5)." value="{{ old('periodicidad') }}" min="5" step="5">
          </div>
          @error('periodicidad', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label class="col-form-label noselect">
              <input type="checkbox" id="edit_notificacion" name="notificacion" @if(old('notificacion')) checked @endif>
              Crear notificación
            </label>
          </div>
          <div class="form-notificacion">
            <hr>
            <div class="mb-3">
              <label for="notif_nombre" class="col-form-label">Nombre de la Notificación <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('notif_nombre', 'update') is-invalid @enderror" id="edit_notif_nombre" name="notif_nombre" placeholder="Nombre" value="{{ old('notif_nombre') }}" required>
            </div>
            @error('notif_nombre', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="mb-3">
              <label for="plantilla_id" class="col-form-label">Plantilla para el correo/sms <span class="text-danger">*</span></label>
              <select class="form-control @error('plantilla_id', 'update') is-invalid @enderror" id="edit_plantilla_id" name="plantilla_id">
                <option value="">Elija una plantilla</option>
                @foreach ($plantillas as $plantilla)
                  <option value="{{ $plantilla->id }}" {{ (old('plantilla_id') == $plantilla->id ? 'selected':'') }}>{{ $plantilla->nombre }}</option>
                @endforeach
              </select>
            </div>
            @error('plantilla_id', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="mb-3">
              <label class="col-form-label noselect"><input type="checkbox" id="edit_notificar_sms" name="notificar_sms" @if(old('notificar_sms')) checked @endif value="1"> Teléfono</label>
              <input type="text" class="form-control @error('telefono', 'update') is-invalid @enderror" id="edit_telefono" name="telefono" placeholder="Teléfono al cual enviar SMS" value="{{ old('telefono') }}">
            </div>
            @error('telefono', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="mb-3">
              <label class="col-form-label noselect"><input type="checkbox" id="edit_notificar_email" name="notificar_email" @if(old('notificar_email')) checked @endif value="1"> Correo</label>
              <input type="email" class="form-control @error('email', 'update') is-invalid @enderror" id="edit_email" name="email" placeholder="Correo al cual notificar" value="{{ old('email') }}">
            </div>
            @error('email', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-editar-tarea">Modificar tarea</button>
      </div>
    </div>
  </div>
</div>

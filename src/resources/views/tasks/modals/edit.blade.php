<div class="modal fade" id="modal-editar-tarea" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear Usuario</h5>
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
            <label for="periodicidad" class="col-form-label">Periodicidad <span class="text-danger">*</span></label>
            <input type="number" class="form-control @error('periodicidad', 'update') is-invalid @enderror" id="edit_periodicidad" name="periodicidad" placeholder="Periodicidad" value="{{ old('periodicidad') }}" min="5" step="5">
          </div>
          @error('periodicidad', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-editar-tarea">Modificar tarea</button>
      </div>
    </div>
  </div>
</div>

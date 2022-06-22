<div class="modal fade" id="modal-editar-plantilla" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if ($errors->update->any())
        <h6 class="alert alert-danger">Se han producido los siguientes errores:</h6>
        @endif
        <form id="form-editar-plantilla" action="" method="POST">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="PUT"/>
          <input type="hidden" name="id" id="edit_id" value="{{old('id')}}">
          <div class="mb-3">
            <label for="nombre" class="col-form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre', 'update') is-invalid @enderror" id="edit_nombre" name="nombre" placeholder="Nombre de la plantilla" value="{{ old('nombre') }}">
          </div>
          @error('nombre', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="descripcion_larga" class="col-form-label">Descripción larga (email) <span class="text-danger">*</span></label>
            <textarea class="form-control @error('descripcion_larga', 'update') is-invalid @enderror" id="edit_descripcion_larga" name="descripcion_larga" rows="5" placeholder="Descripción larga (email)">{{ old('descripcion_larga') }}</textarea>
          </div>
          @error('descripcion_larga', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="descripcion_corta" class="col-form-label">Descripción corta (SMS) <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('descripcion_corta', 'update') is-invalid @enderror" id="edit_descripcion_corta" name="descripcion_corta" placeholder="Descripción corta (SMS)" value="{{ old('descripcion_corta') }}">
          </div>
          @error('descripcion_corta', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-editar-plantilla">Modificar</button>
      </div>
    </div>
  </div>
</div>

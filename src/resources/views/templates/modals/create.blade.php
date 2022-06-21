<div class="modal fade" id="modal-crear-plantilla" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crear Plantilla</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	@if ($errors->store->any())
      	<h6 class="alert alert-danger">Se han producido los siguientes errores:</h6>
      	@endif
        <form id="form-crear-plantilla" action="{{ route('templates.store') }}" method="POST">
          {{ csrf_field() }}
          <div class="mb-3">
            <label for="nombre" class="col-form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre', 'store') is-invalid @enderror" id="nombre" name="nombre" placeholder="Nombre de la plantilla" value="{{ old('nombre') }}">
          </div>
          @error('nombre', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="descripcion_larga" class="col-form-label">Descripción larga (email) <span class="text-danger">*</span></label>
            <textarea class="form-control @error('descripcion_larga', 'store') is-invalid @enderror" id="descripcion_larga" name="descripcion_larga" rows="5" placeholder="Descripción larga (email)">{{ old('descripcion_larga') }}</textarea>
          </div>
          @error('descripcion_larga', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="descripcion_corta" class="col-form-label">Descripción corta (SMS) <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('descripcion_corta', 'store') is-invalid @enderror" id="descripcion_corta" name="descripcion_corta" placeholder="Descripción corta (SMS)" value="{{ old('descripcion_corta') }}">
          </div>
          @error('descripcion_corta', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-crear-plantilla">Crear nueva plantilla</button>
      </div>
    </div>
  </div>
</div>

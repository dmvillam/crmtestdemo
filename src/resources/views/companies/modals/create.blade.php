<div class="modal fade" id="modal-crear-empresa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Crear Empresa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	@if ($errors->store->any())
      	<h6 class="alert alert-danger">Se han producido los siguientes errores:</h6>
      	@endif
        <form id="form-crear-empresa" action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="mb-3">
            <label for="cedula_juridica" class="col-form-label">Cédula jurídica <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('cedula_juridica', 'store') is-invalid @enderror" id="cedula_juridica" name="cedula_juridica" placeholder="Cédula jurídica" value="{{ old('cedula_juridica') }}">
          </div>
          @error('cedula_juridica', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="nombre" class="col-form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre', 'store') is-invalid @enderror" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}">
          </div>
          @error('nombre', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="telefono" class="col-form-label">Teléfono <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('telefono', 'store') is-invalid @enderror" id="telefono" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}">
          </div>
          @error('telefono', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="email" class="col-form-label">Correo <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email', 'store') is-invalid @enderror" id="email" name="email" placeholder="Correo" value="{{ old('email') }}">
          </div>
          @error('email', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="direccion" class="col-form-label">Dirección <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('direccion', 'store') is-invalid @enderror" id="direccion" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}">
          </div>
          @error('direccion', 'store')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="logo" class="col-form-label">Subir Logo</label>
            <input type="file" class="form-control" name="logo" id="logo">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-crear-empresa">Crear nueva empresa</button>
      </div>
    </div>
  </div>
</div>

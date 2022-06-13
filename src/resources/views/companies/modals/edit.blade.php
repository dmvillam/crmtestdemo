<div class="modal fade" id="modal-editar-empresa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
        <form id="form-editar-empresa" action="" method="POST" enctype="multipart/form-data">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="PUT"/>
          <input type="hidden" name="id" id="edit_id" value="{{old('id')}}">
          <div class="mb-3">
            <label for="cedula_juridica" class="col-form-label">Cédula jurídica <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('cedula_juridica', 'update') is-invalid @enderror" id="edit_cedula_juridica" name="cedula_juridica" placeholder="Cédula jurídica" value="{{ old('cedula_juridica') }}">
          </div>
          @error('cedula_juridica', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="nombre" class="col-form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre', 'update') is-invalid @enderror" id="edit_nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}">
          </div>
          @error('nombre', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="telefono" class="col-form-label">Teléfono <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('telefono', 'update') is-invalid @enderror" id="edit_telefono" name="telefono" placeholder="Teléfono" value="{{ old('telefono') }}">
          </div>
          @error('telefono', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="email" class="col-form-label">Correo <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email', 'update') is-invalid @enderror" id="edit_email" name="email" placeholder="Correo" value="{{ old('email') }}">
          </div>
          @error('email', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="direccion" class="col-form-label">Dirección <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('direccion', 'update') is-invalid @enderror" id="edit_direccion" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}">
          </div>
          @error('direccion', 'update')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="logo" class="col-form-label">Cambiar Logo</label>
            <input type="file" class="form-control" name="logo" id="edit_logo">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-editar-empresa">Modificar</button>
      </div>
    </div>
  </div>
</div>

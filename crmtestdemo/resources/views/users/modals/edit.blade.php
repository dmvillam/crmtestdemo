<div class="modal fade" id="modal-user-edit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if ($errors->any())
        <h6 class="alert alert-danger">Se han producido los siguientes errores:</h6>
        @endif
        <form id="form-editar-usuario" action="" method="POST">
          {{ csrf_field() }}
          <input type="hidden" name="_method" value="PUT"/>
          <input type="hidden" name="id" id="edit_id" value="{{old('id')}}">
          <div class="mb-3">
            <label for="nombre" class="col-form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="edit_nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}">
          </div>
          @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          {{--
          <div class="mb-3">
            <label for="password" class="col-form-label">Contraseña <span class="text-danger">*</span></label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="edit_password" name="password" placeholder="Contraseña" value="{{ old('password') }}">
          </div>
          @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          --}}
          <div class="mb-3">
            <label for="cedula" class="col-form-label">Cédula <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('cedula') is-invalid @enderror" id="edit_cedula" name="cedula" placeholder="Cédula" value="{{ old('cedula') }}">
          </div>
          @error('cedula')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="email1" class="col-form-label">Correo 1 <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email1') is-invalid @enderror" id="edit_email1" name="email1" placeholder="Correo 1" value="{{ old('email1') }}">
          </div>
          @error('email1')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="email2" class="col-form-label">Correo 2 <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email2') is-invalid @enderror" id="edit_email2" name="email2" placeholder="Correo 2" value="{{ old('email2') }}">
          </div>
          @error('email2')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="direccion" class="col-form-label">Dirección <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="edit_direccion" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}">
          </div>
          @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="empresa_id" class="col-form-label">Empresa <span class="text-danger">*</span></label>
            <select class="form-control @error('empresa_id') is-invalid @enderror" id="edit_empresa_id" name="empresa_id">
              <option value="">Elija la empresa</option>
              @foreach ($empresas as $empresa)
                <option value="{{ $empresa->id }}" {{ (old('empresa_id') == $empresa->id ? 'selected':'') }}>{{ $empresa->nombre }}</option>
              @endforeach
              <option>Elija un rol</option>
            </select>
          </div>
          @error('empresa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="mb-3">
            <label for="rol_id" class="col-form-label">Rol <span class="text-danger">*</span></label>
            <select class="form-control @error('rol_id') is-invalid @enderror" id="edit_rol_id" name="rol_id">
              <option value="">Elija un rol</option>
              @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" {{ (old('rol_id') == $rol->id ? 'selected':'') }}>{{ $rol->nombre }}</option>
              @endforeach
            </select>
          </div>
          @error('rol_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-editar-usuario">Modificar</button>
      </div>
    </div>
  </div>
</div>

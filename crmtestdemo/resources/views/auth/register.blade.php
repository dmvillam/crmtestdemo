@extends('layouts.basic')

@section('title', "Registrarse")
@section('form-title', "Registrarse")
    
@section('content')
        <form class="" method="POST" action="{{ route('register') }}">
            @csrf
          <div class="form-floating mb-3">
            <input type="text" class="form-control rounded-3 @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required autocomplete="off" autofocus>
            <label for="floatingInput">Nombre</label>
            @error('nombre')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control rounded-3 @error('cedula') is-invalid @enderror" id="cedula" name="cedula" value="{{ old('cedula') }}" required autocomplete="off" autofocus>
            <label for="floatingInput">Cédula</label>
            @error('cedula')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <div class="form-floating mb-3">
            <input type="email" class="form-control rounded-3 @error('email1') is-invalid @enderror" id="email1" name="email1" value="{{ old('email1') }}" required autocomplete="off" autofocus>
            <label for="floatingInput">Correo 1</label>
            @error('email1')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <div class="form-floating mb-3">
            <input type="email" class="form-control rounded-3 @error('email2') is-invalid @enderror" id="email2" name="email2" value="{{ old('email2') }}" required autocomplete="off" autofocus>
            <label for="floatingInput">Correo 2</label>
            @error('email2')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control rounded-3 @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion') }}" required autocomplete="off" autofocus>
            <label for="floatingInput">Dirección</label>
            @error('direccion')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="off">
            <label for="floatingPassword">Contraseña</label>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control rounded-3" id="confirm-password" name="password_confirmation" required autocomplete="off">
            <label for="floatingPassword">Confirmar Contraseña</label>
          </div>
          <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Registrarse</button>
        </form>
@endsection
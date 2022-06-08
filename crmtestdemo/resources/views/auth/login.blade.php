@extends('layouts.basic')

@section('title', "Iniciar sesión")
@section('form-title', "Iniciar sesión")
    
@section('content')
        <form class="" method="POST" action="{{ route('login') }}">
            @csrf
          <div class="form-floating mb-3">
            <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" id="floatingInput" placeholder="name@example.com" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            <label for="floatingInput">Correo</label>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" id="floatingPassword" placeholder="Password" name="password" required autocomplete="current-password">
            <label for="floatingPassword">Contraseña</label>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
          <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit">Acceder</button>
        </form>
@endsection
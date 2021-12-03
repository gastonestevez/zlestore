@extends('layouts.layout')
@section('titulo')
ZLE - Usuarios
@endsection
@section('main')

<div class="uk-container primer-div">

  <form class="uk-text-center uk-width-large uk-background-muted uk-margin-auto login-form uk-padding-large uk-margin-bottom" action="{{ route('login') }}" method="post">
    @csrf
    <fieldset class="uk-fieldset">

        <legend class="uk-legend">Iniciar Sesion</legend>
        @error('email')
              <p style="color: red;">Datos incorrectos</p>
          @enderror

        <div class="uk-margin">
          <div class="uk-inline">
            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: mail"></span>
            <input class="uk-input" value="{{ old('email') }}" type="email" placeholder="Email" name="email" id="email" required autofocus>
          </div>
        </div>

        <div class="uk-margin">
          <div class="uk-inline">
            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
            <input class="uk-input" type="password" placeholder="Clave" name="password" id="password" required>
          </div>
        </div>

          <div class="uk-margin">
            <label for="remember">Recordarme</label>
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          </div>

          <div class="uk-margin">
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}">Olvidaste tu clave? Recuperala aquí</a>
            @endif
          </div>

      <button class="uk-button uk-button-default">Iniciar</button>

    </fieldset>
  </form>


</div>
@endsection

@extends('layouts.layout')
@section('titulo')
ZLE - Editar Usuario
@endsection
@section('main')

  <div class="uk-container primer-div">

    <a href="{{ url()->previous() }}" style="color:black;">Volver</a>

    @if($errors->any())
      {!! implode('', $errors->all('<div>:message</div>')) !!}
    @endif

    {{-- Se muestra el formulario de registrar un nuevo usuario --}}

    <form class="uk-align-center" style="text-align: center;" action="/edituser/{{$user->id}}" method="post">
      @method('put')
      @csrf
      <fieldset class="uk-fieldset">

          <legend class="uk-legend">Editar Usuario</legend>

          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: user"></span>
              <input value="{{$user->name}}" class="uk-input" type="text" placeholder="Usuario" name="name" id="name" required autofocus>
            </div>
          </div>

          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: mail"></span>
              <input value="{{$user->email}}" class="uk-input" type="email" placeholder="Email" name="email" id="email" required>
            </div>
          </div>

          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
              <input class="uk-input" type="password" placeholder="Nueva clave" name="password" id="password">
            </div>
          </div>

          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
              <input class="uk-input" type="password" placeholder="Repetir Clave" name="password_confirmation" id="password_confirmation">
            </div>
          </div>

          <div class="uk-margin">
        <div uk-form-custom="target: > * > span:first-child">
            <select name="role" required>
                <option value="">Seleccione un Rol</option>
                <option value="admin" {{($user->role == "admin")?'selected': '' }}>Administrador</option>
                <option value="employee" {{($user->role == "employee")?'selected': '' }}>Gestor de Tienda</option>
            </select>
            <button class="uk-button uk-button-default" type="button" tabindex="-1">
                <span></span>
                <span uk-icon="icon: chevron-down"></span>
            </button>
        </div>

        <button class="uk-button uk-button-default">Enviar</button>
    </div>

      </fieldset>
    </form>

  </div>


@endsection

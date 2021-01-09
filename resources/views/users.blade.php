@extends('layouts.layout')
@section('titulo')
ZLE - Usuarios
@endsection
@section('main')

  <div class="uk-container primer-div">

    @if($errors->any())
        {!! implode('', $errors->all('<div>:message</div>')) !!}
    @endif

    {{-- Se muestra el formulario de registrar un nuevo usuario --}}

    <form class="uk-align-center" style="text-align: center;" action="/adduser" method="post">
      @csrf
      <fieldset class="uk-fieldset">

          <legend class="uk-legend">Registrar Usuario</legend>

          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: user"></span>
              <input class="uk-input" type="text" placeholder="Usuario" name="name" id="name" required autofocus>
            </div>
          </div>

          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: mail"></span>
              <input class="uk-input" type="email" placeholder="Email" name="email" id="email" required>
            </div>
          </div>

          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
              <input class="uk-input" type="password" placeholder="Clave" name="password" id="password" required>
            </div>
          </div>

          <div class="uk-margin">
            <div class="uk-inline">
              <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: lock"></span>
              <input class="uk-input" type="password" placeholder="Repetir Clave" name="password_confirmation" id="password_confirmation" required>
            </div>
          </div>

          <div class="uk-margin">
        <div uk-form-custom="target: > * > span:first-child">
            <select name="role" required>
                <option value="">Seleccione un Rol</option>
                <option value="admin">Administrador</option>
                <option value="employee">Gestor de Tienda</option>
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

    <hr>

    {{-- Se muestran las cards de todos los usuarios con posibilidad de editar o borrar --}}

    @foreach ($users as $user)

      <div class="uk-card uk-card-default uk-card-hover uk-width-1-4@m">

        <div class="uk-card-header">
            <div class="uk-grid-small uk-flex-middle" uk-grid>
                <div class="uk-width-expand">
                    <div class="uk-card-badge uk-label"  style="background: @if($user->role == 'admin') purple @elseif($user->role == 'employee')  green @endif;">{{$user->role}}</div>
                    <h3 class="uk-card-title uk-margin-remove-bottom" style="padding-top:40px;">{{$user->name}}</h3>
                    <p class="uk-text-meta uk-margin-remove-top"><time datetime="2016-04-01T19:00">{{$user->email}}</time></p>
                </div>
            </div>
        </div>
        <div class="uk-card-footer uk-flex uk-flex-between">
            <a href="/user/{{$user->id}}" class="uk-button uk-button-text">Editar</a>
            <form class="" action="/deleteuser/{{$user->id}}" method="post">
              @method('delete')
              @csrf
              <button type="submit" class="uk-button uk-button-text">Eliminar</a>
            </form>
        </div>

      </div>

      <br>

    @endforeach

      <br><br>

    {{-- <div class="uk-card uk-card-default uk-card-hover uk-width-1-4@m">

      <div class="uk-card-header">
          <div class="uk-grid-small uk-flex-middle" uk-grid>
              <div class="uk-width-expand">
                  <div class="uk-card-badge uk-label">Admin</div>
                  <h3 class="uk-card-title uk-margin-remove-bottom">Tato</h3>
                  <p class="uk-text-meta uk-margin-remove-top"><time datetime="2016-04-01T19:00">taten@dakota.com</time></p>
              </div>
          </div>
      </div>
      <div class="uk-card-footer uk-flex uk-flex-between">
          <a href="/user/{{$id}}" class="uk-button uk-button-text">Editar</a>
          <a href="#" class="uk-button uk-button-text">Eliminar</a>
      </div>

    </div> --}}

    <br><br><br>

  </div>


@endsection

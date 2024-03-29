{{-- https://youtu.be/7QWZxjgvEQc?t=1204 video importante que explica--}}
{{-- Ejemplo de personalizar el mensaje del modal en la vista--}}
{{-- @include('partials.confirm',['url'=>'/deleteproduct/'.$product->id,'mensaje' => 'seguro?']) --}}

{{-- Si no le pasamos name o id se envian como parametros vacios --}}
@php
  if (!isset($name)){
    $name = '';
  }
  if (!isset($id)){
    $id = '';
  }

  if (!isset($method)){
    $method = 'delete';
  }

  if (!isset($primaryButton)){
    $primaryButton = 'Aceptar';
  }

  if (!isset($secondaryButton)){
    $secondaryButton = 'Cancelar';
  }
// dd($id, $message, $enableModalDescription);
@endphp

<div id="confirm{{$method}}{{$id}}" uk-modal>

  <div class="uk-modal-dialog">
    <button class="uk-modal-close-default" type="button" uk-close></button>

    <div class="uk-modal-header">
        <h2 class="uk-modal-title">Confirmación</h2>
    </div>

    <div class="uk-modal-body">
      <p>{{ $message ?? "Esta seguro que lo desea eliminar?"}}</p>
      @if (isset($enableModalDescription))
        <div id='modalDescription'>
        </div>
      @endif
    </div>

    <div class="uk-modal-footer uk-text-right">
      <form id='formConfirmationModal' action="{{$url}}" method="post">
        <button class="uk-button uk-button-default uk-modal-close" type="button">{{$secondaryButton}}</button>
        @method($method)
        @csrf
        <input type="hidden" name="{{$name}}" value="{{$id}}">
        <button class="uk-button uk-button-secondary" type="submit">{{$primaryButton}}</button>
      </form>
    </div>
  </div>
</div>

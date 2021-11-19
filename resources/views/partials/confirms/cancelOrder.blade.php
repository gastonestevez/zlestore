{{-- https://youtu.be/7QWZxjgvEQc?t=1204 video importante que explica--}}
{{-- Ejemplo de personalizar el mensaje del modal en la vista--}}
{{-- @include('partials.confirm',['url'=>'/deleteproduct/'.$product->id,'mensaje' => 'seguro?']) --}}

<div id="cancel{{$id}}" uk-modal>

    <div class="uk-modal-dialog">
      <button class="uk-modal-close-default" type="button" uk-close></button>
  
      <div class="uk-modal-header">
          <h2 class="uk-modal-title">Confirmación</h2>
      </div>
  
      <div class="uk-modal-body">
        <p>{{ $message ?? "Esta seguro que lo desea eliminar?"}}</p>
      </div>
  
      <div class="uk-modal-footer uk-text-right">
        <form action="{{$url}}" method="post">
          <button class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
          @csrf
          <button class="uk-button uk-button-secondary" type="submit">Aceptar</button>
        </form>
      </div>
    </div>
  </div>
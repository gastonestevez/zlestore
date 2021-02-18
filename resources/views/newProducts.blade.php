@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div uk-margin">
    <h1 class="uk-heading-divider">Distribuir productos</h1>

    @if ($products)
    <div class="uk-alert-warning" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        <p>Se han detectado nuevos productos en la plataforma. Distribuya el stock en los depósitos.</p>
    </div>
    @else
    <div class="uk-alert-primary" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        <p>No hay productos nuevos para distribuir.</p>
    </div>
    @endif
    @if (!$warehousesCount)
    <div class="uk-alert-danger" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        <p>No hay depósitos disponibles. Pruebe agregar uno haciendo click <a href="{{url('/warehouse/create')}}">aquí</a>.</p>
    </div>
    @endif


    @if ($warehousesCount)
    @foreach ($products as $item)
    <form action="/newProducts/store" class="boxform uk-grid-1-3@m" method="post">
        @csrf
        <legend class="uk-legend">{{$item->name}}</legend>
        <p class="uk-text">SKU: {{$item->sku}}</p>
        <p class="uk-text">Precio: ${{$item->price}}</p>
        <div class="uk-margin">
            <input type="hidden" name='itemId' value="{{$item->id}}">
            @foreach ($warehouses as $warehouse)
            <label for="stock" class="uk-form-label">Depósito: {{$warehouse->name}} @ {{$warehouse->address}}</label>
            <input type="hidden" name="warehouse[{{$loop->index}}][id]" value='{{$warehouse->id}}'>
            <div class="uk-form-controls">
                <input type="number" name="warehouse[{{$loop->index}}][stock]" class="uk-input uk-form-width-small" value='1' min="1">
            </div>
            @endforeach
        </div>
        <button type="submit" class="uk-button uk-button-default">Distribuir</button>
    </form>
    @endforeach
    @endif

      
</div>

@endsection
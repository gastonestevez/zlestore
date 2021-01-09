@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">
    <h1 class="uk-heading-divider">Agregar depósito</h1>
    <form class="uk-form-horizontal uk-margin-large" action="{{url('warehouse/create')}}" method="POST">
        @csrf
        <div class="uk-margin">
            <label class="uk-form-label" for="form-horizontal-text">Nombre</label>
            <div class="uk-form-controls">
                <input class="uk-input" name='name' id="form-horizontal-text" type="text" placeholder="Nombre del depósito">
            </div>
        </div>

        <div class="uk-margin">
            <label class="uk-form-label" for="form-horizontal-text">Dirección</label>
            <div class="uk-form-controls">
                <input class="uk-input" name='address' id="form-horizontal-text" type="text" placeholder="Dirección del depósito">
            </div>
        </div>
        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid">
            <label><input class="uk-checkbox" name='visibility' value='1' type="checkbox" checked> ¿Es visible?</label>
        </div>
        <button type="submit" class="uk-button uk-button-default">Crear</button>
    </form>
</div>

@endsection
@extends('layouts.layout')
@section('titulo')
ZLE - CSV IMPORT SECTION
@endsection
@section('main')

@if(\Session::has('success'))
    <div class="uk-alert-success" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        <p>{{\Session::get('success')}}</p>
    </div>
@endif
<div class="uk-container primer-div text-center">
    <h2>Gestor de CSV</h2>
    <p>Sección para cargar un csv con toda la base.</p>
    <p>Tenga en cuenta que la estructura del csv está contenida con el siguiente ejemplo:</p>
    <p>1523,ZLE-861-10MU,Guirnalda 10 leds (x10 unidades),visible,800,1,</p>
    <form action="{{ route('csv-import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
            <div class="custom-file text-left">
                <input type="file" name="file" class="custom-file-input" id="customFile">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
        </div>
        <button class="btn btn-primary">Importar datos</button>
    </form>
</div>

@endsection
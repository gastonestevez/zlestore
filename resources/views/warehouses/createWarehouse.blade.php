@extends('layouts.layout')
@section('titulo')
ZLE - Crear depósito
@endsection
@section('main')

 <div class="uk-container primer-div">

    @if($errors->any())
      {!! implode('', $errors->all('<div>:message</div>')) !!}
    @endif

    {{-- Se muestra el formulario de crear nuevo depósito --}}

    <form class="uk-align-center" style="text-align: center;" action="{{url('warehouse/store')}}" method="POST">
        @csrf
        <fieldset class="uk-fieldset">

            <legend class="uk-legend">Crear nuevo Depósito</legend>
            
            <div class="uk-margin">
                <div class="uk-inline">
                  <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: file-text"></span>
                  <input value="{{old('name')}}" class="uk-input" type="text" placeholder="Nombre del depósito" name="name" id="name" required autofocus>
                </div>
            </div>


            <div class="uk-margin">
                <div class="uk-inline">
                 <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: home"></span>
                 <input value="{{old('address')}}" class="uk-input" name='address' id="form-horizontal-text" type="text" placeholder="Dirección del depósito" required>
                </div>
            </div>

        {{-- <div class="uk-flex-center uk-margin uk-grid-small uk-child-width-auto uk-grid">
            <label><input class="uk-checkbox" name='visibility' value='1' type="checkbox" checked> ¿Es visible?</label>
        </div> --}}
        <button type="submit" class="uk-button uk-button-default">Crear</button>
    </form>

    <br>

    <div class="uk-heading-divider"></div>

    <br>

    <legend class="uk-legend">Editar Depósitos</legend>

    @foreach ($warehouses as $warehouse)

        <form class="uk-align-center" style="text-align: center;" action="/warehouse/update/{{$warehouse->id}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('put')

            <fieldset class="uk-fieldset">

                
                <div class="uk-margin">
                    <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: file-text"></span>
                    <input value="{{old('name', $warehouse->name)}}" class="uk-input" type="text" placeholder="Nombre del depósito" name="name" id="name" required autofocus>
                    </div>
                </div>


                <div class="uk-margin">
                    <div class="uk-inline">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: home"></span>
                    <input value="{{old('address', $warehouse->address)}}" class="uk-input" name='address' id="form-horizontal-text" type="text" placeholder="Dirección del depósito" required>
                    </div>
                </div>

            {{-- <div class="uk-flex-center uk-margin uk-grid-small uk-child-width-auto uk-grid">
                <label><input class="uk-checkbox" name='visibility' value='1' type="checkbox" checked> ¿Es visible?</label>
            </div> --}}
            <button type="submit" class="uk-button uk-button-info">Editar</button>
            <a class="uk-button uk-button-danger"  href="#confirmdelete{{$warehouse->id}}" uk-toggle>Eliminar</a>
            {{-- <label for="eliminar{{$warehouse->id}}" type="submit" class="uk-button uk-button-danger">Eliminar</label> --}}
        </form>

        @include('partials.confirms.confirm',['url'=>"/warehouse/delete/{$warehouse->id}", 'message'=>"Seguro quiere eliminar el depósito {$warehouse->name}?", 'name'=>'warehouse_id', 'id'=>"{$warehouse->id}"])
        
    @endforeach
</div>

@endsection

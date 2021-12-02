@extends('layouts.layout')
@section('titulo')
ZLE - Concepts
@endsection
@section('main')


<div class="uk-container primer-div">
    
    <h1 class="uk-heading-divider">Todos los conceptos</h1>

    <legend class="uk-legend uk-text-center uk-padding">Crear conceptos</legend>
    
    <form class="uk-flex uk-align-center uk-flex-center" action="{{route('createConcept')}}" method="POST">
        @csrf
        @method('post')
        
        <div class="uk-card uk-card-default uk-card-hover uk-width-1-4@m">  

            <div class="uk-card-header">
                <div class="uk-grid-small uk-flex-middle" uk-grid>
                    <div class="uk-width-expand uk-inline">
                        <label for="">Nombre del concepto</label>
                        <input class="uk-input" type="text" name='name' value='{{old('name')}}'>
                    </div>
                </div>
            </div>          
            
            <div class="uk-card-footer uk-flex uk-flex-between">
                <button class="uk-button uk-button-default" type="submit">Crear</button>
            </div>

        </div>

    </form>

    <legend class="uk-legend uk-text-center uk-padding">Editar conceptos</legend>

    @foreach ($concepts as $concept)
        <form class="uk-flex uk-align-center uk-flex-center" action="{{route('updateConcept')}}" method="POST">
            @csrf
            @method('put')

            <input type="hidden" name='id' value="{{$concept->id}}">

            <div class="uk-card uk-card-default uk-card-hover uk-width-1-4@m">

                <div class="uk-card-header">
                    <div class="uk-grid-small uk-flex-middle" uk-grid>
                        <div class="uk-width-expand uk-inline">
                            <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: nut"></span>
                            <input value="{{old('name', $concept->name)}}" class="uk-input" type="text" placeholder="Nombre del concepto" name="name" id="name" required>
                        </div>
                    </div>
                </div>

                <div class="uk-card-footer uk-flex uk-flex-center">
                    <a href="#confirmput{{$concept->id}}" class="uk-button uk-button-default" uk-toggle>Editar</a>
                    {{-- <button class="uk-button uk-button-default">Editar</button> --}}
                    <a href="#confirmdelete{{$concept->id}}" class="uk-button uk-button-default" uk-toggle>Eliminar</a>
                </div>
                
            </div>
        </form>

        @include('partials.confirms.confirm',['url'=>"/deleteConcept", 'message'=>"Seguro quiere eliminar el concepto {$concept->name}?", 'name'=>'id', 'id'=>"{$concept->id}"])
        @include('partials.confirms.confirm',['url'=>"/updateConcept", 'message'=>"Seguro quiere editar el concepto {$concept->name}?", 'name'=>'id', 'id'=>"{$concept->id}", 'method'=>"put"])            


    @endforeach

</div>

@endsection
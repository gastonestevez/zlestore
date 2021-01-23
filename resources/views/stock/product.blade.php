@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">


  <h1 class="uk-heading-divider">{{$product->first()->name}}</h1>

    <button class="uk-button uk-button-secondary uk-margin">ACTUALIZAR LISTA</button>

    <div class="uk-child-width-1-2@s uk-grid-match uk-margin" uk-grid>
        @foreach ($warehouses as $warehouse)
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
                <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i>Stock en {{$warehouse->name}}</h3>

              <form method="post" action="">
                @csrf
                @method('put')
                <div class="uk-margin" uk-margin>
                  <div uk-form-custom="target: true">
                    <input class="uk-input uk-form-width-medium" type="number" placeholder="Stock disponible" value="{{$warehouse->getProductStock($warehouse->id, $product->id)}}" >
                  </div>
                  <button class="uk-button uk-button-default">Actualizar</button>
                </div>
              </form>

            </div>
        </div>
        @endforeach
    </div>


</div>

@endsection

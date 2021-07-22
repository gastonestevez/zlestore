@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">


  <h1 class="uk-heading-divider">{{$product->first()->name}}</h1>

    <div class="uk-child-width-1-2@s uk-grid-match uk-margin" uk-grid>
        @foreach ($warehouses as $warehouse)
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
                <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> Stock en {{$warehouse->name}}</h3>
                <p class="">Producto: {{$product->name}} <br>WOO ID: {{$product->woo_id}} <br> SKU: {{$product->sku}}<p>

              <form method="post" action="/updatingStock/{{$product->id}}">
                @csrf
                @method('put')
                <input name="warehouse_id" value="{{$warehouse->id}}" hidden type="hidden">
                <div class="uk-margin uk-flex uk-flex-wrap uk-flex-between" uk-margin>
                  <div>
                    <span>Unidades:</span><br>
                    <div uk-form-custom="target: true">
                      <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: pencil"></span>
                      <input required min="0" name="quantity" class="uk-input uk-form-width-medium" type="number" placeholder="Unidades disponibles" value="{{old('stock', $warehouse->getProductStock($warehouse->id, $product->id))}}">                    
                    </div>
                  </div>
                  <div>
                    <span>Cajas:</span><br>
                    <div uk-form-custom="target: true">
                      <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: pencil"></span>
                      <input required min="0" name="units_in_box" class="uk-input uk-form-width-medium" type="number" placeholder="Cajas disponibles" value="{{old('stock', intval($warehouse->getProductStock($warehouse->id, $product->id)/$product->units_in_box))}}">
                    </div>
                  </div>
                  <br><br>
                </div>
                <button uk-tooltip="Editar Stock" class="uk-button uk-button-default">Actualizar</button>
              </form>

            </div>
        </div>
        @endforeach
    </div>


</div>

@endsection

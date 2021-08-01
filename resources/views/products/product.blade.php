@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">


  <h1 class="uk-heading-divider">{{$product->first()->name}}</h1>

    <div class="uk-child-width-1-1@s uk-grid-match uk-margin" uk-grid>
        @foreach ($warehouses as $warehouse)
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
                <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> Stock en {{$warehouse->name}}</h3>
                <p class="">Producto: {{$product->name}} <br>WOO ID: {{$product->woo_id}} <br> SKU: {{$product->sku}} <br> Unidades por Caja: {{$product->units_in_box}}<p>

              
            @if(auth()->check() && auth()->user()->role == 'admin')            
              
              <form method="post" action="/updatingUnits/{{$product->id}}">
                @csrf
                @method('put')
                <input name="warehouse_id" value="{{$warehouse->id}}" hidden type="hidden">         
                <span>Unidades:</span><br>
                <div uk-form-custom="target: true">
                  <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: pencil"></span>
                  <input required min="0" name="quantity" class="uk-input uk-form-width-medium" type="number" placeholder="Unidades disponibles" value="{{old('stock', $warehouse->getProductStock($warehouse->id, $product->id))}}">                    
                </div>
                <button uk-tooltip="Editar Unidades" class="uk-button uk-button-default">Actualizar</button>
              </form>

              <form method="post" action="/updatingBoxes/{{$product->id}}">
                @csrf
                @method('put')
                <input name="warehouse_id" value="{{$warehouse->id}}" hidden type="hidden">         
                <span>Cajas:</span><br>
                  <div uk-form-custom="target: true">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: pencil"></span>
                    <input required min="0" name="boxes" class="uk-input uk-form-width-medium" type="number" placeholder="Cajas disponibles"
                    @if ($warehouse->getProductStock($warehouse->id, $product->id) > 0)
                     value="{{old('stock', intval($warehouse->getProductStock($warehouse->id, $product->id)/$product->units_in_box))}}"     
                    @else
                     value="0"
                    @endif
                    >        
                  </div>
                <button uk-tooltip="Editar Cajas" class="uk-button uk-button-default">Actualizar</button>
              </form>  

             @endif 

             @if(auth()->check() && auth()->user()->role == 'employee')
              <div class="uk-flex uk-flex-wrap">
                <div class="pr">
                  <span>Unidades:</span><br>
                  <input required min="0" name="quantity" class="uk-input uk-form-width-medium" type="number" placeholder="Unidades disponibles" value="{{old('stock', $warehouse->getProductStock($warehouse->id, $product->id))}}" readonly disabled><br>                    
                </div>
                <div>
                  <span>Cajas:</span><br>
                  <input required min="0" name="boxes" class="uk-input uk-form-width-medium" type="number" placeholder="Cajas disponibles" readonly disabled
                  @if ($warehouse->getProductStock($warehouse->id, $product->id) > 0)
                     value="{{old('stock', intval($warehouse->getProductStock($warehouse->id, $product->id)/$product->units_in_box))}}"     
                    @else
                     value="0"
                    @endif
                    >   
                </div>  
              </div>   
             @endif
              
              <form method="post" action="/transferingUnits/{{$product->id}}">
                @csrf
                @method('put')
                <br>
                <span>Transferir Stock:</span><br>
                <input type="hidden" name="warehouseOrigin" value={{$warehouse->id}}>
                <input required min="1" max="{{$warehouse->getProductStock($warehouse->id, $product->id)}}" name="quantity" class="uk-input uk-form-width-medium" type="number" placeholder="Unidades a transferir" value="">                    
                <span uk-icon="arrow-right"></span>
                <select class="uk-select uk-form-width-medium" name="warehouseDestiny" required>Depósito a transferir
                  <option value="">Depósito destino</option>
                  @foreach ($warehouses as $transferWarehouse)
                    @if ($transferWarehouse != $warehouse)
                    <option value="{{$transferWarehouse->id}}">{{$transferWarehouse->name}}</option>                           
                    @endif
                  @endforeach                 
                </select>
                <button uk-tooltip="Transferir Unidades" class="uk-button uk-button-default">Transferir Unidades</button>
              </form> 
              <form method="post" action="/transferingBoxes/{{$product->id}}">
                @csrf
                @method('put')
                <br>
                <input type="hidden" name="warehouseOrigin" value={{$warehouse->id}}>
                <input required step="1" 
                @if($warehouse->getProductStock($warehouse->id, $product->id) > 0)
                max="{{$warehouse->getProductStock($warehouse->id, $product->id) / $product->units_in_box}}"
                @else
                max="0"
                @endif 
                min="1" name="quantity" class="uk-input uk-form-width-medium" type="number" placeholder="Cajas a transferir" value="">                   
                <span uk-icon="arrow-right"></span>               
                <select class="uk-select uk-form-width-medium" name="warehouseDestiny" required>Depósito a transferir
                  <option value="">Depósito destino</option>
                  @foreach ($warehouses as $transferWarehouse)
                    @if ($transferWarehouse != $warehouse)
                    <option value="{{$transferWarehouse->id}}">{{$transferWarehouse->name}}</option>                           
                    @endif
                  @endforeach                  
                </select>
                <button uk-tooltip="Transferir Cajas" class="uk-button uk-button-default">Transferir Cajas</button>
              </form> 
              

            </div>
        </div>
        @endforeach
    </div>

</div>

@endsection

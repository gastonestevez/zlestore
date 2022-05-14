@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">


  <h1 class="uk-heading-divider">{{$product->name}}</h1>

    
    {{-- modal historial --}}

    <a class="uk-button uk-button-default" href="#modal-overflow" uk-toggle>Historial de movimientos</a>

<div id="modal-overflow" uk-modal>
    <div class="uk-modal-dialog" style="width:100%;">

        <button class="uk-modal-close-default" type="button" uk-close></button>

        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Movimientos de stock {{$product->name}}</h2>
        </div>

        <div class="uk-modal-body" uk-overflow-auto>

          <div class="uk-overflow-auto">
            <table class="uk-table uk-table-small uk-table-divider">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th class="text-nowrap">Depósito origen</th>
                        <th class="text-nowrap">Depósito destino</th>
                        <th class="text-nowrap">Stock restante</th>
                        <th>Responsable</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($movements as $movement)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($movement->created_at)->format('d-m-Y H:i')}}</td>
                        <td>{{$product->name}}</td>
                        <td>{{$movement->quantity}}</td>
                        <td>{{$movement->warehouseOrigin->name}}</td>
                        <td>{{$movement->warehouseDestiny->name}}</td>
                        <td>{{$movement->remaining_stock}}</td>
                        <td>{{$movement->user->name}}</td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
          </div>

        </div>

        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-primary uk-modal-close" type="button">Cerrar</button>
        </div>

    </div>
</div>



    <div class="uk-child-width-1-2 uk-grid-match uk-margin" uk-grid>
        @forelse ($warehouses as $warehouse)
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
                <h3 class="uk-card-title">@if($warehouse->type == 'storage')<i class="fas fa-warehouse icon"></i>@else <i class="fas fa-store-alt"></i></i>@endif Stock en {{$warehouse->name}}</h3>
                <p class="">Producto: {{$product->name}} <br>WOO ID: {{$product->id}} <br> SKU: {{$product->sku}} <br> Unidades por Caja: {{$product->units_in_box}}<p>

              
            @if(auth()->check() && auth()->user()->role == 'admin')            
              
              <form method="post" action="{{route('updatingUnits', $product->id)}}">
                @csrf
                @method('put')
                <input name="warehouse_id" value="{{$warehouse->id}}" hidden type="hidden">         
                <span>Cantidad:</span><br>
                <div uk-form-custom="target: true">
                  <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: pencil"></span>
                  <input required min="0" name="quantity" class="uk-input uk-form-width-medium" type="number" placeholder="Unidades disponibles" value="{{old('stock', $warehouse->getProductStock($warehouse->id, $product->id))}}">                    
                </div>
                <button uk-tooltip="Editar Unidades" class="uk-button uk-button-default">Actualizar</button>
              </form>

              {{-- <form method="post" action="/updatingBoxes/{{$product->id}}">
                @csrf
                @method('put')
                <input name="warehouse_id" value="{{$warehouse->id}}" hidden type="hidden">         
                <span>Cajas:</span><br>
                  <div uk-form-custom="target: true">
                    <span class="uk-form-icon uk-form-icon-flip" uk-icon="icon: pencil"></span>
                    <input required min="0" name="boxes" class="uk-input uk-form-width-medium" type="number" placeholder="Cajas disponibles"
                    @if ($warehouse->getProductStock($warehouse->id, $product->id) > 0 && !empty($product->units_in_box))
                     value="{{old('stock', intval($warehouse->getProductStock($warehouse->id, $product->id)/$product->units_in_box))}}"     
                    @else
                     value="0"
                    @endif
                    >        
                  </div>
                <button uk-tooltip="Editar Cajas" class="uk-button uk-button-default">Actualizar</button>
              </form>   --}}

             @endif 

             @if(auth()->check() && auth()->user()->role == 'employee')
              <div class="uk-flex uk-flex-wrap">
                <div class="pr">
                  <span>Cantidad:</span><br>
                  <input required min="0" name="quantity" class="uk-input uk-form-width-medium" type="number" placeholder="Cantidad disponibles" value="{{old('stock', $warehouse->getProductStock($warehouse->id, $product->id))}}" readonly disabled><br>                    
                </div>
                {{-- <div>
                  <span>Cajas:</span><br>
                  <input required min="0" name="boxes" class="uk-input uk-form-width-medium" type="number" placeholder="Cajas disponibles" readonly disabled
                  @if ($warehouse->getProductStock($warehouse->id, $product->id) > 0)
                     value="{{old('stock', intval($warehouse->getProductStock($warehouse->id, $product->id)/$product->units_in_box))}}"     
                    @else
                     value="0"
                    @endif
                    >   
                </div>   --}}
              </div>   
             @endif
              
              <form method="post" action="{{route('transferingUnits', $product->id)}}">
                @csrf
                @method('put')
                <br>
                <span>Transferir Stock:</span><br>
                <input type="hidden" name="warehouseOrigin" value={{$warehouse->id}}>
                <input required min="1" max="{{$warehouse->getProductStock($warehouse->id, $product->id)}}" name="quantity" class="uk-input uk-form-width-medium" type="number" placeholder="Cantidad a transferir" value="">                    
                <span uk-icon="arrow-right"></span>
                <select class="uk-select uk-form-width-medium" name="warehouseDestiny" required>Depósito a transferir
                  <option value="{{$warehouses->first()->id}}">{{$warehouses->first()->name}}</option>
                  
                  @foreach ($warehouses as $transferWarehouse)
                    @if ($transferWarehouse != $warehouse && $transferWarehouse != $warehouses->first())
                    <option value="{{$transferWarehouse->id}}">{{$transferWarehouse->name}}</option>                           
                    @endif
                  @endforeach                 
                </select>
                <button uk-tooltip="Transferir Unidades" class="uk-button uk-button-default mt-3">Transferir Unidades</button>
              </form> 
              {{-- <form method="post" action="/transferingBoxes/{{$product->id}}">
                @csrf
                @method('put')
                <br>
                <input type="hidden" name="warehouseOrigin" value={{$warehouse->id}}>
                <input required step="1" 
                @if($warehouse->getProductStock($warehouse->id, $product->id) > 0 && !empty($product->units_in_box))
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
              </form>  --}}
              

            </div>
        </div>
        @empty
          <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> No Existen depósitos. <a href="{{route('editWarehouses')}}">Crear un nuevo depósito</a></h3>

        @endforelse
    </div>

</div>

@endsection

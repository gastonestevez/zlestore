@extends('layouts.layout')
@section('titulo')
ZLE - Movimientos de Stock
@endsection
@section('main')

<div class="uk-container primer-div">

    <h4 class=" uk-heading-line uk-text-center uk-margin-top"> <span>Historial - Ventas offline</span></h4>
    <h5>Búsqueda</h5>
    <form class="searchForm uk-search uk-search-default" method="get">
        <div class="pr uk-margin-bottom">
            <select class="uk-select" name="origin">
                <option value="">D.Origen</option>
                @foreach ($warehouses as $warehouse)
                <option value="{{$warehouse->id}}" {{($request->origin == $warehouse->id)?'selected':''}} >{{$warehouse->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="pr uk-margin-bottom">
            <select class="uk-select" name="destiny">
                    <option value="">D.Destino</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{$warehouse->id}}" {{($request->destiny == $warehouse->id)?'selected':''}} >{{$warehouse->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="pr uk-margin-bottom">
            <input value="{{old('createdAt', $request->createdAt)}}" type="date" class="uk-search-input" name="createdAt">
        </div>
        <button class="uk-button uk-button-default limpiar-busqueda" style="margin-right: 15px; margin-bottom: 15px;">Buscar</button>
        <div class="pr uk-margin-bottom">
            <label for="limpiar" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Limpiar Búsqueda</label>
        </div>
    </form>

    <form class="uk-search uk-search-default" style="pointer-events: none;" method="get">
        <button id='limpiar' hidden class="uk-button uk-button-default limpiar-busqueda">Limpiar Búsqueda</button>
    </form>

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Cant</th>
                    <th>D.origen</th>
                    <th>D.destino</th>
                    <th>Stock restante</th>
                    <th>Responsable</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                <tr>
                    <td>{{$movement->id}}</td>
                    <td class="text-nowrap">@if(isset($movement->created_at)) {{ Carbon\Carbon::parse($movement->created_at)->format('d-m-Y H:i')}} @endif</td>
                    <td class="text-nowrap">{{getProduct($movement->product_id)->name}}</td>
                    <td>{{$movement->quantity}}</td>
                    <td>{{$movement->warehouseOrigin->name}}</td>
                    <td>{{$movement->warehouseDestiny->name}}</td>
                    <td>{{$movement->remaining_stock}}</td>
                    <td>{{$movement->user->name}}</td>
                </tr>

                @empty
                    <h3 class="uk-card-title"><i class="fa-solid fa-cart-flatbed-boxes"></i></i> Sin resultados.</h3>
                @endforelse

            </tbody>
        </table>
        {{ $movements->appends($_GET)->links() }}

    </div>

</div>

@endsection
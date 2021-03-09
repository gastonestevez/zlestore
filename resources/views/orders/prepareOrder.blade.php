@extends('layouts.layout')
@section('titulo')
ZLE - Preparar pedido
@endsection
@section('main')

<div class="uk-container primer-div">
    <h1 class="uk-heading-divider">Preparar pedido</h1>
    <table class="uk-table uk-table-striped uk-table-hover">
        <thead>
            <tr>
                <th>N° Orden</th>
                <th>Fecha</th>
                <th>Estado</th> 
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$order->id}}</td>
                <td>{{$order->date_created}}</td>
                <td>{{$order->status}}</td>
                <td>$ {{$order->total}}</td>
            </tr>
        </tbody>
    </table>
    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-striped uk-table-hover">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Producto</th>
                    <th>Unidades</th>
                    <th>Total</th>
                    @foreach ($warehouses as $w)
                        <th>{{$w->name}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($order->line_items as $item)
                    <tr class='item-column'>
                        <td>{{$item->sku}}</td>
                        <td>{{$item->name}}</td>
                        <td class='item-column-quantity'>{{$item->quantity}}</td>
                        <td>$ {{$item->total}}</td>
                        @foreach ($warehouses as $w)
                        <td>
                            <input class="uk-input warehouse-input" type="number" placeholder="{{$w->name}}" value="0" min="0" max={{$w->getProductStock($w->id, $item->localId)}}>
                            <small>{{$w->getProductStock($w->id, $item->localId)}} en stock</small>
                        </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>


@endsection

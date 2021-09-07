@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Lista de Pedidos</h1>


    <a href="/orders" class="uk-button uk-button-secondary uk-margin">ACTUALIZAR PEDIDOS</a>

  <div class="uk-overflow-auto">

    <table class="uk-table uk-table-striped uk-table-hover">
      <thead>
          <tr>
              <th>N° Orden</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Cliente</th>
              <th>SKU</th>
              <th>Producto</th>
              <th>Unidades</th>
              <th>Total</th>
              {{-- <th>Gestionar</th> --}}
              <th></th>
          </tr>
      </thead>
      <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{$order->id}}</td>
                <td>{{$order->date_created}}</td>
                <td>{{$order->status}}</td>
                <td>{{$order->customerName}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>${{number_format()$order->total, 0,',','.')}}</td>
                <td><form action="{{'prepare/'.$order->id}}" method="get"><button class="uk-button uk-button-default" type="submit">Preparar</button></form></td>
                <td><a href="" uk-icon="icon: close"></a></td>
            </tr>
            @foreach ($order->line_items as $item)
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>{{$item->sku}}</td>
              <td>{{$item->name}}</td>
              <td>{{$item->quantity}}</td>
              <td>${{number_format($item->total, 0,',','.')}}</td>
              <td></td>
            </tr>
            @endforeach
        @endforeach
      </tbody>
    </table>

  </div>

</div>

@endsection

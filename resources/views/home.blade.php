@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Lista de Pedidos</h1>


    <button class="uk-button uk-button-secondary uk-margin">ACTUALIZAR LISTA</button>

    <div class="uk-flex">

      <div class="uk-margin pr">
        <form class="uk-search uk-search-default">
          <span class="uk-search-icon-flip" uk-search-icon></span>
          <input class="uk-search-input" type="search" placeholder="SKU ...">
        </form>
      </div>

      <form>
        <select class="uk-select">
          <option>Estado</option>
          <option>Pendientes</option>
          <option>Completados</option>
        </select>
      </form>

    </div>

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
              <th>Cajas</th>
              <th>Gestionar</th>
              <th>Total</th>
          </tr>
      </thead>
      <tbody>
        @foreach ($orders as $order)
        @if ($order->status != 'completed' && $order->status != 'canceled')
          <tr>
            <td>{{$order->number}}</td>
            <td>{{$order->date_created}}</td>
            <td>{{$order->status}}</td>
            <td>{{$order->customerName}}</td>
            <td /> <td /> <td /> <td />
            
            <form action="prepare/{{$order->id}}" method="post">
            @csrf
            <td><button class="uk-button uk-button-secondary" type="submit">Preparar</button></td>
            </form>
            
            <td>{{$order->total}}</td>
          </tr>
          @foreach ($order->line_items as $item)
          <tr class="item">
            <td /> <td /> <td /> <td />
            <td>{{$item->sku}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->quantity}}</td>
            <td>{{$item->quantity / ($item->unidades_por_caja ?: 1)}}</td>
            <td /> <td /> 
          </tr>
          @endforeach
        @endif
        @endforeach
        
      </tbody>
    </table>

  </div>

</div>


@endsection

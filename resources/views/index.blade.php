@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')
@if(\Session::has('noWarehouses'))
<div class="uk-alert-danger" uk-alert>
  <a class="uk-alert-close" uk-close></a>
  <p>{{\Session::get('noWarehouses')}} Pruebe agregar uno haciendo click <a href="{{route('editWarehouses')}}">aquí</a>.</p>
</div>
@endif
<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Sistema de Gestión de Stock</h1>

  <div class="uk-child-width-1-2@s uk-grid-match uk-margin" uk-grid>

    <div>
      <a href="{{route('wcOrders')}}">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <h3 class="uk-card-title"><i class="fas fa-laptop-house"></i> Ver Pedidos online</h3>
            <p>Lista detallada de pedidos realizados en la tienda online</p>
            <br>
            {{-- <a href="/wcOrders" class="uk-link-heading"><i class="fas fa-list-alt"></i> Actualmente existen {{$orders}} pedidos pendientes</a> --}}
        </div>
      </a>
    </div>

    <div>
      <a href="{{route('createOrder')}}">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <h3 class="uk-card-title"><i class="fas fa-people-carry"></i> Armar pedido</h3>
            <p>Armar un pedido desde aquí</p>
            <br>
        </div>
      </a>
    </div>

    <div>
      <a href="{{route('stockList')}}">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <h3 class="uk-card-title"><i class="fas fa-cubes"></i> Stock de Productos</h3>
            <p>Lista de stock de todos los productos existentes</p>
            <a href="{{route('stockList')}}" class="uk-link-heading"><i class="fas fa-list-alt"></i> Actualmente existen {{$products}} productos</a>
        </div>
      </a>
    </div>

    <div>
      <a href="{{route('warehouses')}}">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> Depósitos</h3>
            <p>Lista de las depósitos existentes</p>
            <a href="{{route('warehouses')}}" class="uk-link-heading"><i class="fas fa-list-alt"></i> Actualmente existen {{$warehouses}} depósitos</a>
        </div>
      </a>
    </div>

    <div>
      <a href="{{route('historySales')}}">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <h3 class="uk-card-title"><i class="fas fa-barcode"></i> Historial de ventas offline</h3>
            <p>Lista de las ventas realizadas en el local</p>
        </div>
      </a>
    </div>

    <div style="cursor:not-allowed">
      <a href="#" style="cursor:not-allowed">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <span class="uk-label uk-margin-left" style="background:purple; font-size: 10px;">Próximamente</span>
            <h3 class="uk-card-title"><i class="fas fa-qrcode"></i> Historial de ventas online</h3>
            <p>Lista de las ventas realizadas en la tienda online</p>
        </div>
      </a>
    </div>

    <div>
      <a href="{{route('historyMovements')}}">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            {{-- <span class="uk-label uk-margin-left" style="background:purple; font-size: 10px;">Próximamente V3</span> --}}
            <h3 class="uk-card-title"><i class="fas fa-truck-loading"></i> Historial de movimientos de stock</h3>
            <p>Lista de movimientos de stock entre depósitos/locales</p>
        </div>
      </a>
    </div>

</div>

@endsection

@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')
@if(\Session::has('noWarehouses'))
<div class="uk-alert-danger" uk-alert>
  <a class="uk-alert-close" uk-close></a>
  <p>{{\Session::get('noWarehouses')}} Pruebe agregar uno haciendo click <a href="{{url('/warehouse/new')}}">aquí</a>.</p>
</div>
@endif
<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Sistema de Gestión de Stock</h1>

  <div class="uk-child-width-1-2@s uk-grid-match uk-margin" uk-grid>

    <div>
      <a href="/wcOrders">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <h3 class="uk-card-title"><i class="fas fa-people-carry"></i> Pedidos</h3>
            <p>Lista detallada de pedidos de los clientes</p>
            <br>
            {{-- <a href="/wcOrders" class="uk-link-heading"><i class="fas fa-list-alt"></i> Actualmente existen {{$orders}} pedidos pendientes</a> --}}
        </div>
      </a>
    </div>

    <div>
      <a href="/products/stock">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <h3 class="uk-card-title"><i class="fas fa-cubes"></i> Stock de Productos</h3>
            <p>Lista de todos los productos existentes</p>
            <a href="/products/stock" class="uk-link-heading"><i class="fas fa-list-alt"></i> Actualmente existen {{$products}} productos</a>
        </div>
      </a>
    </div>

    <div>
      <a href="/warehouse/list">
        <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
            <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> Depósitos</h3>
            <p>Lista de los depósitos existentes</p>
            <a href="/warehouse/list" class="uk-link-heading"><i class="fas fa-list-alt"></i> Actualmente existen {{$warehouses}} depósitos</a>
        </div>
      </a>
    </div>

</div>

@endsection

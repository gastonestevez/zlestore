@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Productos</h1>


    <button class="uk-button uk-button-secondary uk-margin">ACTUALIZAR LISTA</button>

    <div class="uk-flex uk-flex-wrap uk-flex-center uk-flex-left@m">

      <div class="pr uk-margin-bottom">
        <form class="uk-search uk-search-default" method="get">
           <a href="" class="uk-search-icon-flip" uk-search-icon></a>
          <input class="uk-search-input" type="search" placeholder="SKU ..." name="sku" value="{{old('sku')}}">
        </form>
      </div>
      <div class="pr uk-margin-bottom">
        <form class="uk-search uk-search-default" method="get">
           <a href="" class="uk-search-icon-flip" uk-search-icon></a>
          <input class="uk-search-input" type="search" placeholder="Nombre ..." name="name">
        </form>
      </div>
      <div class="pr uk-margin-bottom">
        <form class="uk-search uk-search-default" method="get">
           <a href="" class="uk-search-icon-flip" uk-search-icon></a>
          <input class="uk-search-input" type="search" placeholder="Precio ..." name="price">
        </form>
      </div>

    </div>

  <div class="uk-overflow-auto">

    <table class="uk-table uk-table-striped uk-table-hover">
      <thead>
          <tr>
              <th></th>
              <th>SKU</th>
              <th>Nombre</th>
              <th>Precio</th>
              <th>Woo_id</th>
              <th>Acción</th>
          </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)
          <tr>
              <td>{{ $product->id }}</td>
              <td>{{ $product->sku }}</td>
              <td>{{ $product->name }}</td>
              <td>{{ $product->price }}</td>
              <td>{{ $product->woo_id }}</td>
              <td><button class="uk-button uk-button-default" type="button">Gestionar</button></td>
              <td><a href="" uk-icon="icon: close"></a></td>
          </tr>
        @endforeach

      </tbody>
    </table>

  </div>

  {{$products->links()}}

</div>

@endsection

@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Productos</h1>
  @if(\Session::has('noWarehouses'))
    <div class="uk-alert-danger" uk-alert>
      <a class="uk-alert-close" uk-close></a>
      <p>{{\Session::get('noWarehouses')}} Pruebe agregar uno haciendo click <a href="{{url('/warehouse/create')}}">aquí</a>.</p>
    </div>
  @endif
  @if(\Session::has('success'))
    <div class="uk-alert-success" uk-alert>
      <a class="uk-alert-close" uk-close></a>
      <p>{{\Session::get('success')}}</p>
    </div>
  @endif
  <a href={{route('syncWoocommerce')}} onclick="handleSync()" id="syncButton">
    <button class="uk-button uk-button-secondary uk-margin">SINCRONIZAR LISTA</button>
  </a>

    <div class="uk-flex uk-flex-wrap uk-flex-center uk-flex-left@m">

      <div class="pr uk-margin-bottom">
        <form class="uk-search uk-search-default" method="get">
           <a href="" class="uk-search-icon-flip" uk-search-icon></a>
          <input class="uk-search-input" type="search" placeholder="SKU ..." name="sku">
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
              <td>{{ (int)$product->price }}</td>
              <td>{{ $product->woo_id }}</td>
              <td>  <a class="uk-button uk-button-default" href="/stock/products/{{$product->woo_id}}">Gestionar</a></td>
              <td><a href="" uk-icon="icon: close"></a></td>
          </tr>
        @endforeach

      </tbody>
    </table>

  </div>

  {{$products->links()}}

</div>
<script>
  const handleSync = () => {
    const syncButton = document.getElementById('syncButton')
    syncButton.innerHTML = `  
    <button class="uk-button uk-button-secondary uk-margin">
      <i class="fas fa-sync fa-spin"></i> 
      &nbsp;&nbsp;Sincronizando...
    </button>
`
  }
</script>
@endsection



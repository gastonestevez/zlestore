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

    <div class="uk-flex">

      <form class="searchForm uk-search uk-search-default" method="get">
        <div class="pr uk-margin-bottom">
            <input value="{{old('sku', $request->sku)}}" class="uk-search-input" type="search" placeholder="SKU ..." name="sku">
        </div>
        <div class="pr uk-margin-bottom">
            <input value="{{old('sku', $request->woo_id)}}" class="uk-search-input" type="search" placeholder="Woo ID ..." name="woo_id">
        </div>
        <div class="pr uk-margin-bottom">
            <input value="{{old('sku', $request->name)}}" class="uk-search-input" type="search" placeholder="Nombre ..." name="name">
        </div>
        <div class="pr uk-margin-bottom">
            <input value="{{old('sku', $request->price)}}" class="uk-search-input" type="search" placeholder="Precio ..." name="price">
        </div>
        <button class="uk-button uk-button-default limpiar-busqueda" style="margin-right: 15px; margin-bottom: 15px;">Buscar</button>
        <div class="pr uk-margin-bottom">
          <label for="limpiar" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Limpiar Búsqueda</label>
        </div>
     </form>

      <form class="uk-search uk-search-default" method="get">
        <button id='limpiar' hidden class="uk-button uk-button-default limpiar-busqueda">Limpiar Búsqueda</button>
      </form>

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
              {{-- <th>Acción</th> --}}
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
              <td>  <a class="uk-button uk-button-default" href="/product/{{$product->woo_id}}/stock">Ver Stock</a></td>
              {{-- <td><a href="" uk-icon="icon: close"></a></td> --}}
          </tr>
        @endforeach

      </tbody>
    </table>

  </div>

  {{$products->appends(['name' => $request->name, 'sku' => $request->sku, 'woo_id' => $request->woo_id, 'price' => $request->price])->links()}}

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



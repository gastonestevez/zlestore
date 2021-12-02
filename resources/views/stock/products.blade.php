@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Todos los productos</h1>
  @if(\Session::has('noWarehouses'))
    <div class="uk-alert-danger" uk-alert>
      <a class="uk-alert-close" uk-close></a>
      <p>{{\Session::get('noWarehouses')}} Pruebe agregar uno haciendo click <a href="{{url('/warehouses/edit')}}">aquí</a>.</p>
    </div>
  @endif
  {{-- @if(\Session::has('success'))
    <div class="uk-alert-success" uk-alert>
      <a class="uk-alert-close" uk-close></a>
      <p>{{\Session::get('success')}}</p>
    </div>
  @endif --}}
  {{-- <a href={{route('syncWoocommerce')}} onclick="handleSync()" id="syncButton">
    <button class="uk-button uk-button-secondary uk-margin">SINCRONIZAR LISTA</button>
  </a> --}}

    <p>Productos por página: {{count($products)}}</p>

    <div class="uk-flex">

      <form class="searchForm uk-search uk-search-default" method="get">
        <div class="pr uk-margin-bottom">
            <input value="{{old('id', $request->id)}}" class="uk-search-input" type="search" placeholder="ID ..." name="id">
        </div>
        <div class="pr uk-margin-bottom">
            <input value="{{old('sku', $request->sku)}}" class="uk-search-input" type="search" placeholder="SKU ..." name="sku">
        </div>
        <div class="pr uk-margin-bottom">
            <input value="{{old('name', $request->name)}}" class="uk-search-input" type="search" placeholder="Nombre ..." name="name">
        </div>
        <button class="uk-button uk-button-default limpiar-busqueda" style="margin-right: 15px; margin-bottom: 15px;">Buscar</button>
        <div class="pr uk-margin-bottom">
          <label for="limpiar" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Limpiar Búsqueda</label>
        </div>
     </form>

      <form class="uk-search uk-search-default" style="pointer-events: none;" method="get">
        <button id='limpiar' hidden class="uk-button uk-button-default limpiar-busqueda">Limpiar Búsqueda</button>
      </form>

    </div>

  <div class="uk-overflow-auto">

    <table class="uk-table uk-table-striped uk-table-hover">
      <thead>
          <tr>
            <th>Id</th>
            <th>SKU</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock total</th>
            <th>Acciones</th>
          </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)
          <tr>
              <td>{{ $product->id }}</td>
              <td>{{ $product->sku }}</td>
              <td><a href="{{route('productStock', $product->id)}}"> {{ $product->name }} </a></td> 
              <td>${{ number_format($product->price, 0,',','.') }}</td>            
              <td>{{getAllStock($product->id)}}</td>
              <td><a class="uk-button uk-button-default" uk-tooltip="Gestionar Stock" href="/product/{{$product->id}}"><span uk-icon="icon: move"></span></a></td>
              <td></td>
              {{-- <td><a href="" uk-icon="icon: close"></a></td> --}}
          </tr>
        @endforeach

      </tbody>
    </table>

  </div>

  

  {{ $products->appends($_GET)->links() }}

  {{-- {{$products->appends(['name' => $request->name, 'sku' => $request->sku, 'id' => $request->id, 'price' => $request->price])->links()}} --}}

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



@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Productos en {{$warehouse->name}}</h1>
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
  {{-- <a href={{route('syncWoocommerce')}} onclick="handleSync()" id="syncButton">
    <button class="uk-button uk-button-secondary uk-margin">SINCRONIZAR LISTA</button>
  </a> --}}

  <div class="uk-flex">

    {{-- <form class="searchForm uk-search uk-search-default" method="get">
      <div class="pr uk-margin-bottom">
          <input value="{{old('sku', $request->sku)}}" class="uk-search-input" type="search" placeholder="SKU ..." name="sku">
      </div>
      <div class="pr uk-margin-bottom">
          <input value="{{old('woo_id', $request->woo_id)}}" class="uk-search-input" type="search" placeholder="Woo ID ..." name="woo_id">
      </div>
      <div class="pr uk-margin-bottom">
          <input value="{{old('name', $request->name)}}" class="uk-search-input" type="search" placeholder="Nombre ..." name="name">
      </div>
      <button class="uk-button uk-button-default limpiar-busqueda" style="margin-right: 15px; margin-bottom: 15px;">Buscar</button>
      <div class="pr uk-margin-bottom">
        <label for="limpiar" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Limpiar Búsqueda</label>
      </div>
   </form> --}}

    <form class="uk-search uk-search-default" style="pointer-events: none;" method="get">
      <button id='limpiar' hidden class="uk-button uk-button-default limpiar-busqueda">Limpiar Búsqueda</button>
    </form>


  </div>

  <div class="uk-overflow-auto">

    <table class="uk-table uk-table-striped uk-table-hover">
      <thead>
          <tr>
              <th>ID</th>
              <th>SKU</th>
              <th>Nombre</th>
              <th>Precio</th>
              <th>Unidades</th>            
              {{-- <th class="uk-table-shrink">Unidades</th>
              <th class="uk-table-shrink">Cajas</th> --}}
          </tr>
      </thead>
      <tbody>
        @forelse ($products as $product)
          <tr>
              <td>{{ $product->id }}</td>
              <td>{{ $product->sku }}</td>
              <td><a href="/product/{{ $product->id }}/stock"> {{ $product->name }} </a></td>
              <td>${{ number_format($product->price, 0, ',','.') }}</td>
              <td>{{ $product->quantity }}</td>
              <form action="/updatingBoxes/{{$product->id}}" method="POST">
                {{-- @method('put')
                @csrf
                <input name="warehouse_id" value="{{$warehouse->id}}" hidden type="hidden">
                <td><input required min="0" name="quantity" value="{{old('quantity', $warehouse->getProductStock($warehouse->id, $product->id))}}" class="uk-input" placeholder="Stock del producto" readonly disabled></td>                
                <td><input required min="0" name="boxes" value="{{old('boxes', $boxes)}}" class="uk-input" placeholder="Stock del producto" @if(auth()->user()->role != 'admin') readonly disabled @endif ></td>                
                
                @if(auth()->user()->role == 'admin')
                <td><button uk-tooltip="Editar Stock" class="uk-button uk-button-default uk-margin"><span uk-icon="icon: pencil"></span></button></td>
                @endif --}}
                <td><a href="/product/{{$product->id}}/stock" uk-tooltip="Gestionar Stock" class="uk-button uk-button-default uk-margin"><span uk-icon="icon: move"></span></a></td>
              </form>
          </tr>

        @empty
          <h3>No hay productos en éste depósito, <a href="/products/stock">Agregar productos</a></h3>
        @endforelse

      </tbody>
    </table>

  </div>

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



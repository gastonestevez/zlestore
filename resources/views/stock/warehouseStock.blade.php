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
      <p>{{\Session::get('noWarehouses')}} Pruebe agregar uno haciendo click <a href="{{route('/warehouses')}}">aquí</a>.</p>
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
    <div class="pr uk-margin-bottom">
      <input value="{{old('name', $request->price)}}" class="uk-search-input" type="search" placeholder="Precio ..." name="price">
    </div>
    <button class="uk-button uk-button-default limpiar-busqueda" style="margin-right: 15px; margin-bottom: 15px;">Buscar</button>
    <div class="pr uk-margin-bottom">
      <label for="limpiar" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Limpiar Búsqueda</label>
    </div>
    <div class="pr uk-margin-bottom">
      <label for="exportar" uk-tooltip="Exportar depósito a planilla" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Exportar</label>
    </div>
  </form>
  <form class="uk-search uk-search-default" style="pointer-events: none;" method="get">
    <button id='limpiar' hidden class="uk-button uk-button-default limpiar-busqueda">Limpiar Búsqueda</button>
  </form>
  <form class="uk-search uk-search-default"  style="pointer-events: none;" method="get" action="{{route('exportCsv', $warehouse->id)}}">
    <button id='exportar' hidden class="uk-button uk-button-default limpiar-busqueda">Exportar</button>
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
            <th>Uni/caja</th>
            <th>Stock</th>            
            {{-- <th class="uk-table-shrink">Unidades</th>
            <th class="uk-table-shrink">Cajas</th> --}}
        </tr>
      </thead>
      <tbody>
    @forelse ($products as $product)
          <tr>
              <td>{{ $product->id }}</td>
              <td>{{ $product->sku }}</td>
              <td><a href="{{route('productStock', $product->id)}}"> {{ $product->name }} </a></td>
              <td>${{ number_format($product->price, 0, ',','.') }}</td>
              <td>{{ $product->units_in_box }}</td>
              <td>{{ $product->quantity }}</td>
              <form action="{{route('updatingBoxes', $product->id)}}" method="POST">
                {{-- @method('put')
                @csrf
                <input name="warehouse_id" value="{{$warehouse->id}}" hidden type="hidden">
                <td><input required min="0" name="quantity" value="{{old('quantity', $warehouse->getProductStock($warehouse->id, $product->id))}}" class="uk-input" placeholder="Stock del producto" readonly disabled></td>                
                <td><input required min="0" name="boxes" value="{{old('boxes', $boxes)}}" class="uk-input" placeholder="Stock del producto" @if(auth()->user()->role != 'admin') readonly disabled @endif ></td>                
                
                @if(auth()->user()->role == 'admin')
                <td><button uk-tooltip="Editar Stock" class="uk-button uk-button-default uk-margin"><span uk-icon="icon: pencil"></span></button></td>
                @endif --}}
                <td><a href="{{route('productStock', $product->id)}}" uk-tooltip="Gestionar Stock" class="uk-button uk-button-default uk-margin"><span uk-icon="icon: move"></span></a></td>
              </form>
          </tr>
          @empty
          <tr>
            <td>
            <h4>No se encontraron productos en este depósito. <a href="/stock">Agregar productos</a></h4>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>

          </tr>
      @endforelse
    </tbody>
  </table>

  </div>

  {{$products->appends(['name' => $request->name, 'sku' => $request->sku, 'id' => $request->id])->links()}}

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



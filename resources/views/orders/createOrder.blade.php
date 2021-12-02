@extends('layouts.layout')
@section('titulo')
ZLE - Crear orden
@endsection
@section('main')

<div class="uk-container primer-div">

  <h1 class="uk-heading uk-margin-bottom">Armado de pedido</h1>
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

    @if ($orderInProgress)

    <div class="cart-absolute">
      <a href="/orderPreview/{{$orderInProgress->id}}">
        <span style="color:white;" uk-icon="icon: cart"></span>
      </a>
    </div>
    
    <div class="uk-overflow-auto uk-margin-bottom">
      <h4 class=" uk-heading-line  uk-text-center"> <span> Orden en progreso: #{{$orderInProgress->id}}</span></h4>     
      
      <table class="uk-table uk-table-divider uk-table-justify uk-table-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($orderItems as $item)
          <form action="/removeProduct/{{$item->id}}" method="POST">
            @method('DELETE')
            @csrf
            <tr>
              <td>{{$item->product_id}}</td>
              <td>{{$item->product_name}}</td>
              <td>{{$item->product_sku}}</td>
              <td>${{ number_format($item->price, 0,',','.')}}</td>
              <td>{{$item->quantity}}</td>
              <td><button class="uk-button uk-button-default" type="submit" uk-tooltip="Remover producto"><span uk-icon="icon: close"></span></button></td>
            </tr>
          </form>
          @endforeach  
        </tbody>
    </table>
      <div class="uk-flex uk-margin-bottom">
          <strong>Total:&nbsp;</strong> ${{ number_format($orderInProgress->total, 0,',','.')}}
      </div>
      <div class="uk-flex uk-margin-top">
          <a class="uk-button uk-button-default" href="{{route('orderPreview', ['id' => $orderInProgress->id])}}">Confirmar orden</a>
      </div>
      </div>  
    @endif
    <h4 class="uk-heading-line uk-text-center uk-margin-top"> <span>Productos</span></h4>

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
            <th>Stock</th>
            @foreach ($shops as $shop)
                  <th class="uk-text-nowrap">{{$shop->name}}</th>
              @endforeach
            <th></th>
            <th></th>
            {{-- <th>Acción</th> --}}
          </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)
          <tr>
              <td>{{ $product->id }}</td>
              <td>{{ $product->sku }}</td>
              <td><a href="{{route('productStock', $product->id)}}">{{ $product->name }}</a></td> 
              <td>${{ number_format($product->price, 0,',','.') }}</td>            
              <td>{{getAllStock($product->id)}}</td>
              @foreach ($shops as $shop)
                  <td>{{$shop->getProductStock($shop->id, $product->id)}}</td>
              @endforeach
              <td><a class="uk-button uk-button-default" uk-tooltip="Gestionar Stock" href="{{route('productStock',$product->id)}}"><span uk-icon="icon: move"></span></a></td>
              <td class="uk-text-nowrap">
                <form action="{{route('addProductToOrder')}}" method="post">
                  @csrf
                  <input class="uk-input" style="width:80px;" type="number" name="quantity" id="" max="{{getAllStock($product->id)}}" min="1" value="0" required>
                  <input type="hidden" name="productId" value="{{$product->id}}">
                  <input type="hidden" name="name" value="{{$product->name}}">
                  <input type="hidden" name="sku" value="{{$product->sku}}">
                  <input type="hidden" name="price" value="{{$product->price}}">
                  <button class="uk-button uk-button-default" uk-tooltip="Agregar a la orden" type="submit"><span uk-icon="plus-circle"></span></button>
                </form>
                </td>
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



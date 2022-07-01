@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<style>
  thead {
    background-color: #f0f0f0;
  }
  .uk-notification-message {
    background-color: #eaeaea;
    border-radius: 6px;
  }
</style>

<div class="uk-container primer-div">
  <h1 class="uk-heading-divider">Todos los productos</h1>
  @if(\Session::has('noWarehouses'))
    <div class="uk-alert-danger" uk-alert>
      <a class="uk-alert-close" uk-close></a>
      <p>{{\Session::get('noWarehouses')}} Pruebe agregar uno haciendo click <a href="{{url('/warehouses/edit')}}">aquí</a>.</p>
    </div>
  @endif

  <div style="display: flex; justify-content: space-between">
    <p>Productos por página: {{count($products)}}</p>
    <label style="display: flex; justify-content: space-between; align-items:center;"  for="transferCheck">
      <span style="margin-right: 10px" uk-icon="icon: social"></span>
      Transferir entre depósitos
      <input style="margin-left: 20px" type="checkbox" id='transferCheck'>
    </label>
  </div>


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
        <div class="pr uk-margin-bottom">
          <input value="{{old('name', $request->price)}}" class="uk-search-input" type="search" placeholder="Precio ..." name="price">
        </div>
        <button class="uk-button uk-button-default limpiar-busqueda" style="margin-right: 15px; margin-bottom: 15px;">Buscar</button>
        <div class="pr uk-margin-bottom">
          <label for="limpiar" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Limpiar Búsqueda</label>
        </div>
        <div class="pr uk-margin-bottom">
          <button disabled type="button" id='alterStock' class="uk-button uk-button-default limpiar-busqueda">Guardar Stock</button>
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
            @foreach ($storages as $storage)
            <th class="uk-text-nowrap warehouse">{{$storage->name}}</th>
            @endforeach
            <th class="transfer">Origen</th>
            <th class="transfer">Destino</th>
            <th class="transfer">Cantidad</th>
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
              @foreach ($storages as $storage)
                  <td class="warehouse">
                    <input 
                      warehouse-id="{{$storage->id}}" 
                      product-id="{{$product->id}}" 
                      class="uk-input uk-form-width-small stockCount" 
                      type="number" 
                      min="0" 
                      max="9999" 
                      value="{{$storage->getProductStock($storage->id, $product->id)}}"
                    >
                  </td>
              @endforeach            
              <td class="transfer">
                <select product-id="{{$product->id}}" type="text" class="uk-select warehouseInput warehouseFrom" style="width:150px;" name="storage" required>
                  <option value="0" selected value="">Elegir depósito</option>
                  @foreach ($storages as $storage)
                    <option value="{{$storage->id}}">{{$storage->name}}</option>
                  @endforeach     
                </select>
              </td>
              <td class="transfer">
                <select product-id="{{$product->id}}" type="text" class="uk-select warehouseInput warehouseTo" style="width:150px;" name="storage" required>
                  <option value="0" selected value="">Elegir depósito</option>
                  @foreach ($storages as $storage)
                    <option value="{{$storage->id}}">{{$storage->name}}</option>
                  @endforeach     
                </select>
              </td>
              <td class="transfer">
                <input 
                  warehouse-id="{{$storage->id}}" 
                  product-id="{{$product->id}}" 
                  class="uk-input uk-form-width-small transferCount" 
                  type="number" 
                  min="0" 
                  max="9999" 
                  value="0"
                >
              </td>
              <td><a class="uk-button uk-button-default" uk-tooltip="Gestionar Stock" href="/product/{{$product->id}}"><span uk-icon="icon: move"></span></a></td>

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
  $("table").stickyTableHeaders();
  $(".transfer").hide();
  $(".warehouse").show();
  let stockList = [];
  let transferList = [];

  $("#transferCheck").on("click", function(){
    if($(this).is(":checked")){
      $(".transfer").show();
      $(".warehouse").hide();
      $("#alterStock").html("Transferir stock");
    }else{
      $(".transfer").hide();
      $(".warehouse").show();
      $("#alterStock").html("Guardar stock");
    }
  });

  $(".stockCount").on("change", function(e) {
    $("#alterStock").prop("disabled", false);
    const productId = e.currentTarget.attributes['product-id'].value
    const warehouseId = e.currentTarget.attributes['warehouse-id'].value
    const stock = e.currentTarget.value

    const found = stockList.find(item => item.productId == productId && item.warehouseId == warehouseId)
    if(found) {
      found.stock = stock
    } else {
      stockList.push({
        productId: productId,
        warehouseId: warehouseId,
        stock: stock
      })
    }
  });

  $(".transferCount").on("change", function(e) {
    $("#alterStock").prop("disabled", false);
    const productId = e.currentTarget.attributes['product-id'].value
    const stock = e.currentTarget.value

    const found = transferList.find(item => item.productId == productId)
    if(found) {
      found.stock = stock
    } else {
      transferList.push({
        productId: productId,
        stock: stock
      })
    }
  });

  $(".warehouseFrom").on("change", function(e) {
    $("#transferStock").prop("disabled", false);
    const productId = e.currentTarget.attributes['product-id'].value
    const warehouseId = e.currentTarget.value

    const found = transferList.find(item => item.productId == productId)
    if(found) {
      found.warehouseFrom = warehouseId
    } else {
      transferList.push({
        productId: productId,
        warehouseFrom: warehouseId
      })
    }
    console.log(transferList)
  });

  $(".warehouseTo").on("change", function(e) {
    $("#transferStock").prop("disabled", false);
    const productId = e.currentTarget.attributes['product-id'].value
    const warehouseId = e.currentTarget.value

    const found = transferList.find(item => item.productId == productId)
    if(found) {
      found.warehouseTo = warehouseId
    } else {
      transferList.push({
        productId: productId,
        warehouseTo: warehouseId
      })
    }
    console.log(transferList)
  });

  const transferStock = () => {
    const route = `{{route('transferingUnits', 0)}}`
    const token = `{{csrf_token()}}`
    $.ajax({
      url: route,
      type: "PUT",
      data: {
        _token: token,
        batch: true,
        transferList: transferList || []
      },
      success: function(data) {
        console.log(data)
        resetLists();
        location.reload()
      },
      error: function(data) {
        console.error(data)
        console.log(data.responseJSON.message)
        UIkit.notification({
          message: '<span uk-icon=\'icon: warning\'></span> ' + data.responseJSON.message, 
          pos: 'bottom-right', 
          status: 'danger'});
      }
    })
  }

  const resetLists = () => {
    stockList = [];
    transferList = [];
  }

  $("#alterStock").click(function (e) {
    if($("#transferCheck").is(":checked")){
      transferStock();
      return;
    }
    const route = `{{route('updatingUnits', 0)}}`
    const token = `{{csrf_token()}}`
    const data = {
      _token: token,
      noRedirect: true,
      batch: true,
      stockList: stockList
    }
    $.ajax({
      type: 'PUT',
      url: route,
      data: data,
      success: function (data) {
        UIkit.notification({
          message: '<span uk-icon=\'icon: check\'></span> Stock actualizado.', 
          pos: 'bottom-right', 
          status: 'success'});
        resetLists();
      },
      error: function (data) {
        UIkit.notification({
          message: '<span uk-icon=\'icon: warning\'></span> Error al actualizar el stock.', 
          pos: 'bottom-right', 
          status: 'danger'});
      }
    })
  });

</script>
@endsection



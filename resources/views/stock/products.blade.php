@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<style>
  html {
    background-color: #f8fafc;
    width: 100%;
  }
  thead {
    background-color: #f0f0f0;
  }
  .uk-notification-message {
    background-color: #eaeaea;
    border-radius: 6px;
  }
  #tablediv {
    /* overflow: hidden;
    display: block;
    position: relative;
    margin-right: 40px; */
  }
  .uk-container {
    margin-left: 0;
  }
</style>
@php   
@endphp

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
          <button disabled type="button" id='alterStock' class="uk-button uk-button-default limpiar-busqueda alterStock">Guardar Stock</button>
        </div>
    </form>

      <form class="uk-search uk-search-default" style="pointer-events: none;" method="get">
        <button id='limpiar' hidden class="uk-button uk-button-default limpiar-busqueda">Limpiar Búsqueda</button>
      </form>


    </div>

  @if (count($products) > 0)
  <div id="tablediv" class="">

    <table id="table" class="uk-table uk-table-striped uk-table-hover">
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
              @php
                $newStorages = [];
              @endphp
              @foreach ($storages as $storage)
                @php
                  $obj = new \stdClass;
                  $obj->data = $storage;
                  // $obj->stock = $storage->getProductStock($storage->id, $product->id);
                  $newStorages[] = $obj;
                @endphp
              @endforeach
              
              @foreach ($newStorages as $storage)
                  <td class="warehouse">
                    <input 
                      warehouse-id="{{$storage->data->id}}" 
                      product-id="{{$product->id}}"
                      class="uk-input uk-form-width-small stockCount"
                      @auth
                        @if(auth()->user()->role != 'admin')
                          disabled
                        @endif
                      @endauth
                      type="number" 
                      min="0" 
                      max="9999" 
                      value="{{array_key_exists($storage->data->id, $product->stock) ? $product->stock[$storage->data->id] : 0}}"
                    >
                  </td>
              @endforeach            
              <td class="transfer">
                @php
                    $storageSelected = false;
                    $shopSelected = false;
                    $warehouseFrom = $newStorages[0]->data->id;

                @endphp
                <select from-product-id="{{$product->id}}" product-id="{{$product->id}}" type="text" class="uk-select warehouseInput warehouseFrom" style="width:150px;" name="storage" required>
                  @foreach ($newStorages as $storage)
                    @if($storage->data->type != 'shop')           
                      <option
                        value="{{$storage->data->id}}"
                        data-stock="{{array_key_exists($storage->data->id, $product->stock) ? $product->stock[$storage->data->id] : 0}}"
                        @if(!$storageSelected && $storage->data->type == 'storage')
                        @php

                          $storageSelected = true;
                        @endphp
                          selected
                        @endif
                      >{{$storage->data->name}} ({{array_key_exists($storage->data->id, $product->stock) ? $product->stock[$storage->data->id] : 0}})</option>                    
                    @endif  
                  @endforeach

                </select>
              </td>
              <td class="transfer">
                @php
                    $warehouseTo = $newStorages[0]->data->id;
                @endphp
                <select to-product-id="{{$product->id}}" product-id="{{$product->id}}" type="text" class="uk-select warehouseInput warehouseTo" style="width:150px;" name="storage" required>
                  <option value="0" selected value="">Elegir depósito</option>
                  {{-- @php
                    $storages = $storages->sortByDesc(function($storage) use ($product) {
                      return $storage->getProductStock($storage->id, $product->id);
                    });
                  @endphp --}}
                  @foreach ($newStorages as $storage)
                    <option 
                      value="{{$storage->data->id}}"
                      @if (!$shopSelected && $storage->data->type == 'shop')
                        @php
                        $warehouseTo = $storage->data->id;
                          $shopSelected = true;
                        @endphp
                        selected
                      @endif
                    >{{$storage->data->name}} ({{array_key_exists($storage->data->id, $product->stock) ? $product->stock[$storage->data->id] : 0}})</option>
                  @endforeach     
                </select>
              </td>
              <td class="transfer">
                <input 
                  warehouse-from-id="{{$warehouseFrom}}"
                  warehouse-to-id="{{$warehouseTo}}" 

                  product-id="{{$product->id}}" 
                  class="uk-input uk-form-width-small transferCount" 
                  type="number" 
                  min="0" 
                  max="9999" 
                  value="0"
                >
              </td>
              <td>
                <button type="button" id='alterStock' class= "uk-text-nowrap uk-button uk-button-default limpiar-busqueda alterStock">Transferir</button>
              </td>

              {{-- <td><a href="" uk-icon="icon: close"></a></td> --}}
          </tr>
        @endforeach

      </tbody>
    </table>

  </div>

  

  {{ $products->appends($_GET)->links() }}
  @endif
<div id='messagesContainer'></div>
  {{-- {{$products->appends(['name' => $request->name, 'sku' => $request->sku, 'id' => $request->id, 'price' => $request->price])->links()}} --}}

</div>
<script>
  $("table").stickyTableHeaders();
  $(".transfer").hide();
  $(".warehouse").show();
  $("#transferCheck").prop("checked", "checked");
  $(".transfer").show();
  $(".warehouse").hide();
  $(".alterStock").html("Transferir");
  let stockList = [];
  let transferList = [];

  const getInitialValues = () => {
    const from = document.querySelectorAll('.warehouseFrom');
    const to = document.querySelectorAll(".warehouseTo");
    const transferCount = document.querySelectorAll(".transferCount");
    // do an array of objects with from to transferCount
    const initialValues = [];
    for (let i = 0; i < from.length; i++) {
      initialValues.push({
        warehouseFrom: from[i].value,
        warehouseTo: to[i].value,
        stock: transferCount[i].value,
        productId: from[i].getAttribute('product-id')
      });
    }
    return initialValues
    
  }  
  $(document).ready(function () {
    const uware = document.querySelectorAll('.warehouseFrom');
    uware.forEach(u => {
      let optionsArray = []
      for (let index = 0; index < u.options.length; index++) {
          optionsArray.push(u.options[index]);
      }
      optionsArray = optionsArray.sort((a, b) => {
        return parseInt(b.getAttribute('data-stock')) - parseInt(a.getAttribute('data-stock'))
      })
      for (let index = 0; index < u.options.length; index++) {
        u.options[index] = optionsArray[index]
      }

      u.options[0].selected = true
      
    })
    transferList = getInitialValues();
    
    const checkbox = $("#transferCheck")
    checkbox.prop("checked", localStorage.getItem('checked') === 'true')
    if($("#transferCheck").is(":checked")){
      $(".transfer").show();
      $(".warehouse").hide();
      $(".alterStock").html("Transferir");
    }else{
      $(".transfer").hide();
      $(".warehouse").show();
      $(".alterStock").html("Guardar stock");
    }
    
    return;
  });
  
  

 

  $("#transferCheck").on("click", function(){
    localStorage.setItem('checked', localStorage.getItem('checked') === 'true' ? 'false' : 'true')
    location.reload()
  });

  $(".stockCount").on("change", function(e) {
    $(".alterStock").prop("disabled", false);
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
    $(".alterStock").prop("disabled", false);
    const productId = e.currentTarget.attributes['product-id'].value
    const stock = e.currentTarget.value
    const warehouseFrom = document.querySelectorAll(`[from-product-id="${productId}"]`)[0].value
    const warehouseTo = document.querySelectorAll(`[to-product-id="${productId}"]`)[0].value
    const found = transferList.find(item => item.productId == productId)

    if(found) {
      found.stock = stock
      found.warehouseFrom = warehouseFrom
      found.warehouseTo = warehouseTo
    } else {
      transferList.push({
        productId: productId,
        stock: stock,
        warehouseFrom,
        warehouseTo,
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
      if(!found.stock) {
        found.stock = 0
      }
    } else {
      transferList.push({
        productId: productId,
        warehouseFrom: warehouseId,
        stock: 0
      })
    }
  });

  $(".warehouseTo").on("change", function(e) {
    $("#transferStock").prop("disabled", false);
    const productId = e.currentTarget.attributes['product-id'].value
    const warehouseId = e.currentTarget.value

    const found = transferList.find(item => item.productId == productId)
    if(found) {
      found.warehouseTo = warehouseId
      if(!found.stock) {
        found.stock = 0
      }
    } else {
      transferList.push({
        productId: productId,
        warehouseTo: warehouseId,
        stock: 0
      })
    }
  });

  const transferStock = () => {
    const route = `{{route('transferingUnits', 0)}}`
    const token = `{{csrf_token()}}`
    const filteredTransferList = transferList?.filter(item => parseInt(item.stock) > 0) || []
    if(!filteredTransferList.length) {
      showMessage('Debe agregar al menos una unidad a transferir.', 'error')
    } else {
      $.ajax({
        url: route,
        type: "PUT",
        data: {
          _token: token,
          batch: true,
          transferList: filteredTransferList
        },
        success: function(data) {
          resetLists();
          showMessage('¡Transferencia exitosa!', 'success')
          setTimeout(() => { location.reload() }, 3000);
        },
        error: function(data) {
          console.log(data.responseJSON.message)
          showMessage(data.responseJSON?.message, 'error')
        }
      })
    }
  }

  const resetLists = () => {
    stockList = [];
    transferList = [];
  }

  $(".alterStock").click(function (e) {
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
        showMessage('Stock actualizado.', 'success')
        resetLists();
      },
      error: function (data) {
        showMessage('Error al actualizar el stock', 'error')
      }
    })
  });

  const showMessage = (message = '', status) => {

    const html = `
        <div class="wow animated fadeInDown alert sticky-notification notification-${status}">
          ${message}
        </div>
    `
    $('#messagesContainer').html(html)
    $("#messagesContainer").fadeTo(6000, 500).slideUp(500, function(){
    $("#messagesContainer").slideUp(500);
  });
  }


  let table = document.getElementById('table');
  let tablediv = document.getElementById('tablediv');
  let width = table.offsetWidth;
  tablediv.style.width = width + 'px';

</script>
@endsection



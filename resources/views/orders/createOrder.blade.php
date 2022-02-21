@extends('layouts.layout')
@section('titulo')
ZLE - Crear pedido
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
    
    <div class="uk-overflow-auto uk-margin-bottom">
      <h4 class=" uk-heading-line  uk-text-center"> <span> Pedido en progreso: #{{$orderInProgress->id}}</span></h4>     
      
      <table class="uk-table uk-table-divider uk-table-justify uk-table-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Uni/caja</th>
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
              <td>{{getProduct($product->id)->units_in_box}}</td>
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
          <a class="uk-button uk-button-default" href="{{route('orderPreview', ['id' => $orderInProgress->id])}}">Confirmar pedido</a>
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
        
        <button onclick="clearCart()" for="limpiarCarrito" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Vaciar Carrito</button>
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
            <th>Uni/caja</th>
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
              <td>{{getProduct($product->id)->units_in_box}}</td> 
              <td>${{ number_format($product->price, 0,',','.') }}</td>            
              <td>{{getAllStock($product->id)}}</td>
              @foreach ($shops as $shop)
                  <td>{{$shop->getProductStock($shop->id, $product->id)}}</td>
              @endforeach
              <td><a class="uk-button uk-button-default" uk-tooltip="Gestionar Stock" href="{{route('productStock',$product->id)}}"><span uk-icon="icon: move"></span></a></td>
              <td class="uk-text-nowrap">
                <form action="{{route('addProductToOrder')}}" method="post">
                  @csrf
                  <input data-description="{{ $product->name }}" class="uk-input stockInput" style="width:80px;" type="number" name="quantity" id="{{ $product->id }}" 
                  {{-- max="{{getAllStock($product->id)}}" remove stock validation --}}
                  min="0" 
                  value="0" 
                  required>
                  <input type="hidden" name="productId" value="{{$product->id}}">
                  <input type="hidden" name="name" value="{{$product->name}}">
                  <input type="hidden" name="sku" value="{{$product->sku}}">
                  <input type="hidden" name="price" value="{{$product->price}}">
                  {{-- <button class="uk-button uk-button-default" uk-tooltip="Agregar a la orden" type="submit"><span uk-icon="plus-circle"></span></button> --}}
                </form>
                </td>
              {{-- <td><a href="" uk-icon="icon: close"></a></td> --}}
          </tr>
        @endforeach

      </tbody>
    </table>

  </div>

  

  {{ $products->appends($_GET)->links() }}

  <a href="#confirmgetproducts" id='cartAnchor' uk-toggle>
    <div class="cart-absolute" id='cartButton'>
      <span style="color:white;" uk-icon="icon: cart"></span>  
      <span id='badge' class="uk-badge badge-absolute">0</span>
    </div>
  </a>
  {{-- {{$products->appends(['name' => $request->name, 'sku' => $request->sku, 'id' => $request->id, 'price' => $request->price])->links()}} --}}

  <form id="storageForm" action="{{route('addProductToOrder')}}" method="post">@csrf</form>
</div>

@include('partials.confirms.confirm',
  [
    'url'=>"#", 
    'message'=>"Estos son los productos agregados:",
    'name'=>'warehouse_id', 
    'enableModalDescription' => true,
    'id' => 'products',
    'method' => 'get'
  ])


<script>
const stockInputs = document.querySelectorAll('.stockInput')
const cartButton = document.getElementById('cartButton')
const formConfirmationModal = document.getElementById('formConfirmationModal')
const cartAnchor = document.getElementById('cartAnchor')

const clearCart = () => {
  window.localStorage.clear()
}

formConfirmationModal.addEventListener('submit', (e) => {
  e.preventDefault()
  let items = []
  Object.keys(window.localStorage).forEach(i => {
    items.push(JSON.parse(window.localStorage.getItem(i)))
  })
  const storageForm = document.getElementById('storageForm')
  const storageInput = document.createElement('input')
  storageInput.setAttribute('type', 'hidden')
  storageInput.setAttribute('name', 'storageItems')
  storageInput.setAttribute('value', JSON.stringify(items))
  storageForm.appendChild(storageInput)
  clearCart()
  storageForm.submit()
})

const uploadBadgeValues = () => {
  const badge = document.getElementById('badge')
  let totalCount = 0
  Object.keys(window.localStorage).forEach(i => {
    totalCount += parseInt(JSON.parse(window.localStorage.getItem(i)).quantity)
  })
  
  badge.innerHTML = `${totalCount}`
}

const refreshIconCart = () => {
  if(!window.localStorage.length){
    cartButton.style.display = 'none'
  } else {
    cartButton.style.display = 'block'
    uploadBadgeValues()
  }
}

const renderTable = (body) => `
    <table class="uk-table uk-table-divider uk-table-justify uk-table-middle">
      <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
          ${body}
        </tbody>
    </table>
  `

const removeItem = (id) => {
  window.localStorage.removeItem(id)
  const modalDescription = document.getElementById('modalDescription')
  const table = getTableComponent()
  modalDescription.innerHTML = table
  refreshIconCart()
}

const onChangePreviewItem = (id) => {
  const itemInputValue = document.getElementById(`modal-${id}`).value
  const item = JSON.parse(window.localStorage.getItem(id))
  window.localStorage.setItem(id, JSON.stringify({...item, quantity: itemInputValue}));
  refreshIconCart()

}

const renderItem = (item) => `<td>${item}</td>`
const renderInputItem = (item, id = 0) => `<td><input min='1' type="number" id="modal-${id}" onchange="onChangePreviewItem(${id})" value="${item}"></td>`
const removeItemButton = (id) => `<td><button onclick="removeItem(${id})" class="uk-button uk-button-default" uk-tooltip="Remover producto"><span uk-icon="icon: close"></span></button></td>`

const getTableComponent = () => {
  let items = ``
  Object.keys(window.localStorage).forEach(i => {
    const item = JSON.parse(window.localStorage.getItem(i));
    if(item.quantity > 0) {
      items += `
        <tr>
          ${renderItem(item.id)}
          ${renderItem(item.name)}
          ${renderInputItem(item.quantity, item.id)}
          ${removeItemButton(item.id)}
        </tr>`
    }
  });
  return renderTable(items)
}

cartAnchor.addEventListener('click', () => {
  const modalDescription = document.getElementById('modalDescription')
  const table = getTableComponent()
  modalDescription.innerHTML = table
})

const getStartingValue = (id) => {
  const storageItem = window.localStorage.getItem(id)
  return storageItem ? JSON.parse(storageItem).quantity : 0
}

stockInputs.forEach( i => {
    i.value = getStartingValue(i.id)
    i.addEventListener("change", (e) => {
        const quantity = e.currentTarget.value;
        const id = e.currentTarget.id;
        const name = e.currentTarget.getAttribute('data-description')
        if(parseInt(quantity) === 0) {
          console.log('hola');
          window.localStorage.removeItem(id)
        } else {
          window.localStorage.setItem(id, JSON.stringify({id, quantity, name}));
        }
        refreshIconCart()
    });
})


window.onload = () => {
  refreshIconCart()
}

</script>
@endsection



@extends('layouts.layout')
@section('titulo')
ZLE - Crear pedido
@endsection
@section('main')

<style>

  html {
    background-color: #f8fafc;
    width: 100%;
  }
  .probando {
    overflow: hidden;
    width: 2200px;
    display: block;
    position: relative;
  }
  .uk-container {
    margin-left: 0;
  }
</style>

<div class="uk-container primer-div">

  <h1 class="uk-heading uk-margin-bottom">Armado de pedido</h1>
  @if(\Session::has('noWarehouses'))
    <div class="uk-alert-danger" uk-alert>
      <a class="uk-alert-close" uk-close></a>
      <p>{{\Session::get('noWarehouses')}} Pruebe agregar uno haciendo click <a href="{{url('/warehouses/edit')}}">aquí</a>.</p>
    </div>
  @endif

    @if ($orderInProgress)
    
    <div class="uk-overflow-auto uk-margin-bottom">
      <h4 class=" uk-heading-line  uk-text-center"> <span> Pedido en progreso: #{{$orderInProgress->id}}</span></h4>     
      
      <button href="#toggle-animation" class="uk-button uk-button-default" type="button" uk-toggle="target: #toggle-animation; animation: uk-animation-fade">Ver pedido</button>
      <div hidden id="toggle-animation" class="uk-card uk-card-default uk-card-body uk-margin-small">

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
                <td>{{getProduct($item->product_id)->units_in_box}}</td>
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
  
    </div>

      <div class="uk-flex uk-margin-top">
          <a class="uk-button uk-button-default" href="{{route('orderPreview', ['id' => $orderInProgress->id])}}">Confirmar pedido</a>
      </div>
      </div>  
    @endif
    <h4 class="uk-heading-line uk-text-center uk-margin-top"> <span>Productos</span></h4>

    <p>Productos por página: {{count($products)}}</p>

    @include('partials.filters')
    @if (count($products) > 0)

  <div class="probando">  
  <div class="uk-overflow-auto">
    <table class="uk-table uk-table-striped uk-table-hover">
      <thead>
          <tr>
            <th>Id</th>
            <th>SKU</th>
            <th>Nombre</th>
            <th></th>
            <th></th>
            <th>Uni/caja</th>
            <th>Precio</th>
            <th>Stock</th>
            @foreach ($storages as $storage)
                  <th class="uk-text-nowrap">{{$storage->name}}</th>
            @endforeach
          </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)

          <tr>
              <td>{{ $product->id }}</td>
              <td class="uk-text-nowrap">{{ $product->sku }}</td>
              <td class="uk-text-nowrap"><a href="{{route('productStock', $product->id)}}">{{ $product->name }}</a></td> 
              <td><a class="uk-button uk-button-default" uk-tooltip="Gestionar Stock" href="{{route('productStock',$product->id)}}"><span style="width: 20px;" uk-icon="icon: move; ratio: 2"></span></a></td>
              <td class="uk-text-nowrap">
                <form action="{{route('addProductToOrder')}}" method="post">
                  @csrf
                  <input data-description="{{ $product->name }}" class="uk-input stockInput" style="width:80px;" type="number" name="quantity" id="{{ $product->id }}" 
                  {{-- max="{{getAllStock($product->id)}}" remove stock validation --}}
                  min="0" 
                  value="0" 
                  required>
                  <select data-product-id="{{$product->id}}" data-product-name="{{$product->name}}" id="warehouse{{$product->id}}" type="text" class="uk-select warehouseInput" style="width:150px;" name="storage" required>
                    <option value="0" selected value="">Elegir depósito</option>
                    @foreach ($storages as $storage)
                      <option value="{{$storage->id}}">{{$storage->name}}</option>
                    @endforeach     
                  </select>
                  <input type="hidden" name="productId" value="{{$product->id}}">
                  <input type="hidden" name="name" value="{{$product->name}}">
                  <input type="hidden" name="sku" value="{{$product->sku}}">
                  <input type="hidden" name="price" value="{{$product->price}}">
                  {{-- <button class="uk-button uk-button-default" uk-tooltip="Agregar a la orden" type="submit"><span uk-icon="plus-circle"></span></button> --}}
                </form>
              </td>
              <td>{{getProduct($product->id)->units_in_box}}</td> 
              <td>${{ number_format($product->price, 0,',','.') }}</td>            
              <td>{{getAllStock($product->id)}}</td>
              @foreach ($storages as $storage)
                  <td class="uk-text-nowrap">{{$storage->getProductStock($storage->id, $product->id)}}</td>
              @endforeach           
              {{-- @foreach ($shops as $shop)
                  <td>{{$shop->getProductStock($shop->id, $product->id)}}</td>
              @endforeach --}}
              {{-- <td><a href="" uk-icon="icon: close"></a></td> --}}
          </tr>
        @endforeach

      </tbody>
    </table>
  
    
  </div>
</div>

{{ $products->appends($_GET)->links() }}
@endif
  


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
const warehouseInputs = document.querySelectorAll('.warehouseInput')

const clearCart = () => {
  window.localStorage.clear()
}

formConfirmationModal.addEventListener('submit', (e) => {
  e.preventDefault()
  const items = JSON.parse(window.localStorage.getItem('cart')) || []

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
  const cart = JSON.parse(window.localStorage.getItem('cart')) || []
  Object.keys(cart).forEach(i => {
    totalCount += parseInt(cart[i].quantity)
  })
  
  badge.innerHTML = `${totalCount}`
}

const refreshIconCart = () => {
  const cart = JSON.parse(window.localStorage.getItem('cart')) || []

  if(!cart?.length){
    cartButton.style.display = 'none'
    window.localStorage.setItem('cart', JSON.stringify([]))
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
                <th>Depósito</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
          ${body}
        </tbody>
    </table>
  `

const removeItem = (id) => {
  // window.localStorage.removeItem(id)
  const cart = (JSON.parse(window.localStorage.getItem('cart')) || [])
  const filteredCart = cart.filter(item => item.id != id)
  window.localStorage.setItem('cart', JSON.stringify(filteredCart))
  const modalDescription = document.getElementById('modalDescription')
  const table = getTableComponent()
  modalDescription.innerHTML = table
  refreshIconCart()
}

const onChangePreviewItem = (id) => {
  const itemInputValue = document.getElementById(`modal-${id}`).value
  const cart = JSON.parse(window.localStorage.getItem('cart')) || []
  const item = cart.find(item => item.id == id)
  const convertedCart = JSON.stringify(cart.map(item => item.id == id ? {...item, quantity: itemInputValue} : item))
  window.localStorage.setItem('cart', convertedCart)
  // window.localStorage.setItem(id, JSON.stringify({...item, quantity: itemInputValue}));
  refreshIconCart()

}

const renderItem = (item) => `<td>${item}</td>`
const renderInputItem = (item, id = 0) => `<td><input min='1' type="number" id="modal-${id}" onchange="onChangePreviewItem(${id})" value="${item}"></td>`
const renderStorage = (storage) => `<td>${storage ? storage.name : 'No seleccionado'}</td>`
const removeItemButton = (id) => `<td><button onclick="removeItem(${id})" class="uk-button" uk-tooltip="Remover producto"><span style="width:10px;" uk-icon="icon: close"></span></button></td>`

const getTableComponent = () => {
  const storages = @json($storages);
  let items = ``
  const cart = JSON.parse(window.localStorage.getItem('cart')) || []
  cart.forEach(item => {
    const currentStorage = storages.find(s => s.id == item.warehouseId)
    items += `
      <tr>
        ${renderItem(item.id)}
        ${renderItem(item.name)}
        ${renderInputItem(item.quantity, item.id)}
        ${renderStorage(currentStorage)}
        ${removeItemButton(item.id)}
      </tr>
    `
  })
  // Object.keys(window.localStorage).forEach(i => {
  //   const item = JSON.parse(window.localStorage.getItem(i));
  //   if(item.quantity > 0) {
  //     items += `
  //       <tr>
  //         ${renderItem(item.id)}
  //         ${renderItem(item.name)}
  //         ${renderInputItem(item.quantity, item.id)}
  //         ${removeItemButton(item.id)}
  //       </tr>`
  //   }
  // });
  return renderTable(items)
}

cartAnchor.addEventListener('click', () => {
  const modalDescription = document.getElementById('modalDescription')
  const table = getTableComponent()
  modalDescription.innerHTML = table
})

const getStartingValue = (id) => {
  const cart = JSON.parse(window.localStorage.getItem('cart')) || []
  const item = cart.find(item => item.id == id)
  // const storageItem = window.localStorage.getItem(id)
  return item
  // return storageItem ? JSON.parse(storageItem).quantity : 0
}

stockInputs.forEach( i => {
    i.value = getStartingValue(i.id)?.quantity || 0
    i.addEventListener("change", (e) => {
        const quantity = e.currentTarget.value;
        const id = e.currentTarget.id;
        const name = e.currentTarget.getAttribute('data-description')
        const warehouseId = document.getElementById(`warehouse${id}`).value
        if(parseInt(quantity) === 0) {
          const cart = (JSON.parse(window.localStorage.getItem('cart')) || [])
          const filteredCart = cart.filter(item => item.id != id)
          window.localStorage.setItem('cart', JSON.stringify(filteredCart))
          // window.localStorage.removeItem(id)
        } else {
          const cart = (JSON.parse(window.localStorage.getItem('cart')) || [])
          const filteredCart = cart.filter(item => item.id != id)
          const item = {id, name, quantity, warehouseId}
          window.localStorage.setItem('cart', JSON.stringify([...filteredCart, item]))
          // window.localStorage.setItem(id, JSON.stringify({id, quantity, name, warehouseId}));
        }
        refreshIconCart()
    });
})

warehouseInputs.forEach( i => {
    const options = i.options
    const productId = i.getAttribute('data-product-id')
    i.selectedIndex = Array.from(options).find(o => o.value == getStartingValue(productId)?.warehouseId) || 0
    i.value = getStartingValue(productId)?.warehouseId || 0
    // i.value = i. getStartingValue(i.id).warehouseId
    i.addEventListener("change", (e) => {
        const warehouseId = e.currentTarget.value;
        const productId = e.currentTarget.getAttribute('data-product-id');
        const productName = e.currentTarget.getAttribute('data-product-name')
        console.log({
          warehouseId, productId, productName
        })
        const cart = (JSON.parse(window.localStorage.getItem('cart')) || [])
        const filteredCart = cart.filter(item => item.id != productId)

        const item = cart.find(item => item.id == productId)
        if(item){
          const newItem = {...item, warehouseId}
          window.localStorage.setItem('cart', JSON.stringify([...filteredCart, newItem]))
        }
        
      })
})


window.onload = () => {
  refreshIconCart()
}

</script>
@endsection



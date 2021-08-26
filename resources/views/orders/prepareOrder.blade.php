@extends('layouts.layout')
@section('titulo')
ZLE - Preparar pedido
@endsection
@section('main')
<div id='messages'>{{-- Error messages will be displayed here --}}</div> 
<div class="uk-container primer-div">
    <h1 class="uk-heading-divider">Preparar pedido</h1>
    <table class="uk-table uk-table-striped uk-table-hover">
        <thead>
            <tr>
                <th>N° Orden</th>
                <th>Fecha</th>
                <th>Estado</th> 
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$order->id}}</td>
                <td>{{$order->date_created}}</td>
                <td>{{$order->status}}</td>
            </tr>
        </tbody>
    </table>
    <div class="uk-overflow-auto">
        <form id='orderForm' action='{{url('/storeOrder/' . $order->id)}}' method='POST'>

        <table class="uk-table uk-table-striped uk-table-hover">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Producto</th>
                    <th>Unidades</th>
                    @foreach ($warehouses as $w)
                        <th>{{$w->name}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @csrf
                @method('POST')
                @foreach ($order->line_items as $item)
                @php
                $boxes = (int)($item->quantity/$item->units_in_box) > 0 ? (int)($item->quantity/$item->units_in_box) . 'c' : '';
                $units = $item->quantity%$item->units_in_box > 0 ? $item->quantity%$item->units_in_box . 'u' : '';
                $boxesAndUnits = $boxes . $units;
                @endphp
                    <tr class='item-column'>
                        <td>{{$item->sku}}</td>
                        <td>{{$item->name}}</td>
                        <td class='item-column-quantity' >
                            <div uk-tooltip="title: C son Cajas y U son Unidades; pos: left">
                                {{$item->quantity}} 
                                ({{$boxesAndUnits}})
                            </div>
                        </td>
                            @foreach ($warehouses as $w)
                            <td>
                                <input 
                                    name='product[{{$item->localId}}][{{$w->id}}]'
                                    class="uk-input warehouse-input" 
                                    type="number" 
                                    placeholder="{{$w->name}}" 
                                    value="0" 
                                    min="0" 
                                    max={{$w->getProductStock($w->id, $item->localId)}}
                                >
                                <small>{{$w->getProductStock($w->id, $item->localId)}} en stock</small>
                            </td>
                            @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        </form>
    </div>
    <div class="uk-margin">
        <p id='errorMessage'></p>
    </div>
    <div class="uk-margin">
        <div uk-form-custom="target: > * > span:first-child">
            <select id='transitionSelect' name='transition' required>
                <option value=''>Transferir estado a...</option>
                <option value='processing'>Processing</option>
                <option value='completed'>Completed</option>
                <option value='cancelled'>Cancelled</option>
                <option value='refunded'>Refunded</option>
                <option value='failed'>Failed</option>
                <option value='on-hold'>On hold</option>
                <option value='trash'>Trash</option>
                <option value='any'>Any</option>
            </select>
            <button id='transitionSelectButton' class="uk-button uk-button-default" type="button" tabindex="-1">
                <span></span>
                <span uk-icon="icon: chevron-down"></span>
            </button>
        </div>
    </div>
    <div class='uk-margin' >
        <button 
            class="uk-button uk-button-default limpiar-busqueda" 
            style="margin-right: 15px; margin-bottom: 15px;"
            onclick="storeOrder(event)"
        >
            Distribuir
        </button>
    </div>
</div>


<script>

const validateStocks = () => {
    let validated = true
    const items = document.querySelectorAll('.item-column')
    items.forEach(item => {
        quantity = item.querySelector('.item-column-quantity').innerText
        const warehouses = item.querySelectorAll('.warehouse-input')
        let inputCount = 0
        warehouses.forEach(w => {
            inputCount +=  parseInt(w.value)
        })
        if(parseInt(quantity) !== inputCount) {
            validated = false
            item.style.color = 'red'
        } else {
            item.style.color = 'black'
        }
    })
    const selectValue = document.getElementById('transitionSelect').value
    const selectButton = document.getElementById('transitionSelectButton')
    if(!selectValue) {
        validated = false
        selectButton.style.border = '1px solid red'
    } else {
        selectButton.style.border = '1px solid #e5e5e5'
    }
    return validated
}

const displayErrorMessage = () => {
    return (`
    <div class="wow animated fadeInDown alert sticky-notification notification-error">
          Hay uno o mas productos sin distribuir el total de su stock.
        </div>
    `)
}
const storeOrder = (e) => {
    if(validateStocks()) {
        const select = document.getElementById('transitionSelect')
        const transitionSelect = select.cloneNode(true)
        transitionSelect.value = select.value
        const form = document.getElementById('orderForm')
        transitionSelect.style.visibility = 'hidden'
        e.currentTarget.disabled = true
        e.currentTarget.innerHTML = `
            <i class="fas fa-spinner fa-spin"></i> 
            &nbsp;&nbsp;Distribuyendo...
        `
        form.appendChild(transitionSelect)
        form.submit()
    } else {
        const errorMessage = document.getElementById('errorMessage')
        errorMessage.innerHTML = 'Hay uno o mas productos sin distribuir el total de su stock.'
        errorMessage.style.color = 'red'
        document.getElementById('messages').innerHTML = displayErrorMessage()
    }

}
</script>
@endsection

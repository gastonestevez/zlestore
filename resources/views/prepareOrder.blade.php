@extends('layouts.layout')
@section('titulo')
ZLE - Preparar pedido
@endsection
@section('main')

<div class="uk-container primer-div uk-margin">
  <h1 class="uk-heading-divider">Preparar pedido</h1>

  <div class="uk-overflow-auto">
    <table class="uk-table uk-table-striped uk-table-hover">
      <thead>
          <tr>
              <th>N° Orden</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Total</th>
          </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{$order->number}}</td>
          <td>{{$order->date_created}}</td>
          <td>{{$order->status}}</td>
          <td>$ {{$order->total}}</td>
        </tr>
      </tbody>
    </table>
  </div>

  @foreach ($order->line_items as $item)
    <div class="boxform uk-margin">
        @csrf
        <legend class="uk-legend">{{$item->name}}</legend>
        <p class="uk-text">SKU: {{$item->sku}}</p>
        <p class="uk-text">Cantidad: <span id='total'>{{$item->quantity}}</span></p>
        <div class="uk-margin">
            <input type="hidden" name='itemId' value="{{$item->id}}">
            @foreach ($warehouses as $warehouse)
            <label for="stock" class="uk-form-label">Depósito: {{$warehouse->name}} @ {{$warehouse->address}}</label>
            <input type="hidden" name="warehouse[{{$loop->index}}][id]" value='{{$warehouse->id}}'>
            <div class="uk-form-controls">
                <input type="number" name="warehouse[{{$loop->index}}][stock]" class='incremental' onchange="inputNumberChange(event)" class="uk-input uk-form-width-small" value='0' min="0">
            </div>
            @endforeach
        </div>
      </div>
    @endforeach

</div>

<script>
  const totalSelector = document.querySelector('#total')
  const totalOrder = totalSelector.innerHTML

  const inputNumberChange = (e) => {
    console.log(e.target.value)
    const incrementals = document.querySelectorAll('.incremental')
    let finalDiscount = 0
    incrementals.forEach(el => {
      finalDiscount += parseInt(el.value)
    });
    
    totalSelector.innerHTML = totalOrder - finalDiscount
  }
</script>
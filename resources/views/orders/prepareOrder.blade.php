@extends('layouts.layout')
@section('titulo')
ZLE - Preparar pedido
@endsection
@section('main')

<input type="hidden" name="orderId" id='orderId' value='{{$order->id}}'>
<div id='orderContainer'></div>
<script src="/js/app.js"></script>

</div>


@endsection

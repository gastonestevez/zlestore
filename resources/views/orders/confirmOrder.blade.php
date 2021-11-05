@extends('layouts.layout')
@section('titulo')
ZLE - Confirmar Orden
@endsection
@section('main')

{{-- 4956 Variacion
4955 PADRE
4852 SIMPLE --}}

<div class="uk-container primer-div">

    @php
       $taxonomies = [];
    @endphp
    @foreach ($order->orderItems() as $item)
        ID: {{$item->product_id}} <br>
        NOMBRE: {{$item->product_name}} <br>
        SKU: {{$item->product_sku}} <br>
        PRECIO: ${{ number_format($item->price, 0,',','.')}} <br>
        CANTIDAD: {{$item->quantity}} <br>
        @foreach (getProductTaxonomies($item->product_id) as $taxonomy)        
            {{$taxonomy}} <br>
            @if (array_search($taxonomy, $taxonomies) === false)
                @php
                    $taxonomies[] = $taxonomy
                @endphp
            @endif 
        @endforeach
        <button class="uk-button uk-button-default" type="submit">Remover producto</button> <br>
    @endforeach
    {{-- {{dd($taxonomies);}} --}}
    <br>
      ORDER TOTAL: ${{ number_format($order->total, 0,',','.')}}

    <hr class="uk-divider-icon">

    <form action="/createDocument" method="POST">
        @method('post')
        @csrf
        
        <select name="concept_id">
            <option value="1">Venta en el local</option>  
            <option value="2">otros</option>  
        </select>
        <span class="uk-text-lighter">Crear nuevos conceptos</span>

        <label for="">Información adicional</label>
        <textarea name="" id="" cols="30" rows="10"></textarea>

        <button class="uk-button uk-button-default" type="submit">Crear factura</button>
    </form>

    <form action="">
        <select name="" id="">
            <option value="default">Elegi una categoria</option> 
            @foreach ($taxonomies as $taxonomy)
            <option value="{{$taxonomy}}">{{$taxonomy}}</option> 
            @endforeach
        </select>
    </form>

</div>

@endsection
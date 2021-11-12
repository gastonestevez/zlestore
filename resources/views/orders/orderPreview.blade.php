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
        <form action="/removeProduct/{{$item->id}}" method="POST">
            @method('DELETE')
            @csrf
            <button class="uk-button uk-button-default" type="submit">Remover producto</button> <br>
        </form>
    @endforeach
    {{-- {{dd($taxonomies);}} --}}
    <br>
      ORDER TOTAL: ${{ number_format($order->total, 0,',','.')}}

    <hr class="uk-divider-icon">

    <form action="/orderToPending/{{$id}}" method="POST">
        @method('post')
        @csrf
        
        <select name="concept_id" required>
            <option value="">Seleccione una opción</option>
            @foreach ($concepts as $concept)
            <option value="{{$concept->id}}">{{$concept->name}}</option>
            @endforeach  
            <option value="2">otros</option>  
        </select>
        <a href="/concepts" target="_blank" class="uk-button-link uk-text-lighter">Crear/eliminar/editar conceptos</a>

        {{-- <select name="" id="">
            <option value="default">Elegi una categoria</option> 
            @foreach ($taxonomies as $taxonomy)
            <option value="{{$taxonomy}}">{{$taxonomy}}</option> 
            @endforeach
        </select> --}}

        <label for="">Información adicional</label>
        <textarea name="info" id="" cols="30" rows="10"></textarea>

        <button class="uk-button uk-button-default" type="submit">Finalizar orden</button>
    </form>

</div>

@endsection
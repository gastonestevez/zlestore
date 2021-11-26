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
    <h4 class=" uk-heading-line  uk-text-center"> <span>Resumen de orden</span></h4>
    <table class="uk-table uk-table-divider uk-margin-bottom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Tags</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems() as $item)
            <tr>
                <td>{{$item->product_id}} </td>
                <td>{{$item->product_name}} </td>
                <td>{{$item->product_sku}} </td>
                <td>${{ number_format($item->price, 0,',','.')}} </td>
                <td>{{$item->quantity}} </td>
                <td>
                    @foreach (getProductTaxonomies($item->product_id) as $taxonomy)        
                        <span class="uk-badge">{{$taxonomy}}</span>
                        @if (array_search($taxonomy, $taxonomies) === false)
                            @php
                                $taxonomies[] = $taxonomy
                            @endphp
                        @endif 
                    @endforeach
                </td>
                <td>
                    <form action="/removeProduct/{{$item->id}}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button class="uk-button uk-button-default" type="submit">Remover producto</button> <br>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- {{dd($taxonomies);}} --}}
    <div class="uk-margin-top">
        <strong>Total:&nbsp;</strong> ${{ number_format($order->total, 0,',','.')}}
    </div>

    <h4 class=" uk-heading-line  uk-text-center uk-margin-top"> <span>Adicionales</span></h4>


    <form class="uk-form-stacked" action="{{route('orderToPending', $id)}}" method="POST">
        @method('post')
        @csrf
        <div class="uk-margin">
            <div class="uk-form-label">Concepto</div>
                <div class="uk-form-controls">
                    <select class="uk-select" name="concept_id" required>
                        <option value="">Seleccione una opción</option>
                        @foreach ($concepts as $concept)
                        <option value="{{$concept->id}}">{{$concept->name}}</option>
                        @endforeach  
                        <option value="2">otros</option>  
                    </select>
                </div>
            <small>
                <a href="/concepts">Crear/Modificar/Eliminar un concepto</a>
            </small>
        </div>
        
        <div class="uk-margin">
            <div class='uk-form-label'>Información adicional</div>
            <div class="uk-form-controls">
                <textarea class="uk-input" name="info" id="" cols="30" rows="10"></textarea>
            </div>
        </div>

        <div class="uk-margin">
            <div class="uk-form-label">Descuento</div>
                <div class="uk-form-controls">
                    <select class="uk-select" name="category_discount" id="">
                        <option value="default">Elegi una categoria</option> 
                        <option value="all">Todas</option> 
                        @foreach ($taxonomies as $taxonomy)
                        <option value="{{$taxonomy}}">{{$taxonomy}}</option> 
                        @endforeach
                    </select>
                </div>
            </div>
        
        <div class="uk-margin">
            <div class='uk-form-label'>Valor de descuento</div>
            <div class="uk-form-controls">
                <input type="number" name="discount">
            </div>
        </div>


        <div class="uk-margin">
            <button class="uk-button uk-button-default" type="submit">Finalizar orden</button>
        </div>
    </form>

</div>

@endsection
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
    <div name='orderId' hidden id="{{$order->id}}"></div>
    <h4 class=" uk-heading-line  uk-text-center pt-5"> <span>Resumen de orden</span></h4>
    <form class="uk-form-stacked" action="{{route('orderToPending', $id)}}" method="POST">
        @method('post')
        @csrf
    <table class="uk-table uk-table-divider uk-margin-bottom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>SKU</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Uni/caja</th>
                <th>Tags</th>
                <th>Depósito</th>
                <th>Descuento</th>
                <th>Acción</th>
            </tr>
        </thead>
        
        <tbody>

            @foreach ($order->orderItems() as $item)
            <tr>
                <td>{{$item->product_id}}
                    <input type="hidden" name="products[]" value="{{$item->product_id}}">
                </td>
                <td>{{$item->product_name}}</td>
                <td>{{$item->product_sku}}</td>
                <td>${{ number_format($item->price, 0,',','.')}}</td>
                <td>{{$item->quantity}} </td>
                <td>{{getProduct($item->product_id)->units_in_box}}</td>
                <td data-product-id="{{$item->product_id}}" class="discountCategory">
                    @foreach (getProductTaxonomies($item->product_id) as $taxonomy)        
                        <span data-product-id="{{$item->product_id}}" class="uk-badge" style="background: #008F72; padding: 3px 10px 3px 10px;">{{$taxonomy}}</span>
                        @if (array_search($taxonomy, $taxonomies) === false)
                            @php
                                $taxonomies[] = $taxonomy
                            @endphp
                        @endif 
                    @endforeach
                </td>
                <td>{{$item->warehouse ? $item->warehouse->name : 'No seleccionado'}}</td>
                <td>
                    <input 
                        id="discount{{$item->product_id}}" 
                        type="number" 
                        value=0
                        name="discount[]"
                        class="uk-input discountInput" 
                        min=0 
                        max=100
                        data-product-id="{{$item->product_id}}"
                    >
                </td>
                <td>
                    <button onclick="onClickDeleteProduct('{{$item->id}}')" class="uk-button uk-button-default" type="button" uk-tooltip="Remover producto"><span uk-icon="icon: close"></span></button> <br>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="uk-margin-top uk-flex">
        <strong>Total:&nbsp;${{ number_format($order->total, 0,',','.')}}</strong>
    </div>

    <br>
    <h4 class=" uk-heading-line  uk-text-center uk-margin-top"> <span>Adicionales</span></h4>

    <div class="uk-container-xsmall">

        
            <div class="uk-margin">
                <div class="uk-form-label">Concepto</div>
                    <div class="uk-form-controls">
                        <select class="uk-select" name="concept_id">
                            <option value="">Seleccione una opción</option>
                            @foreach ($concepts as $concept)
                            <option value="{{$concept->id}}">{{$concept->name}}</option>
                            @endforeach  
                            <option value="">otros</option>  
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

            <h4 class=" uk-heading-line  uk-text-center uk-margin-top"> <span>Descuentos</span></h4>


            <div class="discounts-container">
                <div class="uk-margin uk-flex uk-flex-middle">
                    <span>Categoria: </span>
                    <select id='taxonomySelect' class="uk-select uk-margin-left">
                        <option value='Todos' selected>Todos</option>
                        @foreach ($taxonomies as $taxonomy)
                            <option value="{{$taxonomy}}">{{$taxonomy}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="uk-margin uk-flex uk-flex-middle">
                    <input id="taxonomyDiscount" class="uk-input" value=0 type="number" min=0 max=100>
                    <button type="button" onclick="addCategoryDiscount()" class="uk-button uk-margin-left">Aplicar</button>
                </div>
            </div>

            <div class="uk-margin">
                <button class="uk-button uk-button-default" type="submit">Finalizar orden</button>
            </div>
        </form>

        <form id="removeProductForm" method="POST">
            @method('DELETE')
            @csrf
        </form>

    </div>

</div>

@endsection
<script>
    
</script>
<script src="/js/discounts.js" charset="utf-8"></script>
<script>
    function show_my_receipt() {
        const orderId = document.getElementsByName("orderId")[0].id
        const date = new Date(Date.now()).toLocaleDateString("es-MX");
        const now = date.replaceAll('/', '');
        const page = '/storage/'+orderId+'_'+now+'.pdf';
    }
</script>
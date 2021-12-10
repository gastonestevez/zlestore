@extends('layouts.layout')
@section('titulo')
ZLE - Historial
@endsection
@section('main')

{{-- 4956 Variacion
4955 PADRE
4852 SIMPLE --}}

<div class="uk-container primer-div">

    @php
        $taxonomies = [];
    @endphp
    <h4 class=" uk-heading-line uk-text-center uk-margin-top"> <span>Historial - Ventas offline</span></h4>
    <h5>Búsqueda</h5>
    <form class="searchForm uk-search uk-search-default" method="get">
        <div class="pr uk-margin-bottom">
            <input value="{{old('id', $request->id)}}" class="uk-search-input" type="search" placeholder="ID ..." name="id">
        </div>
        <div class="pr uk-margin-bottom">
            <input value="{{old('createdAt', $request->createdAt)}}" type="date" class="uk-search-input" name="createdAt">
        </div>
        <button class="uk-button uk-button-default limpiar-busqueda" style="margin-right: 15px; margin-bottom: 15px;">Buscar</button>
        <div class="pr uk-margin-bottom">
            <label for="limpiar" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Limpiar Búsqueda</label>
        </div>
    </form>

    <form class="uk-search uk-search-default" style="pointer-events: none;" method="get">
        <button id='limpiar' hidden class="uk-button uk-button-default limpiar-busqueda">Limpiar Búsqueda</button>
    </form>

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Autor</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th class="uk-table-shrink">Documento</th>
                    <th class="uk-table-shrink">Completar</th>
                    <th class="uk-table-shrink">Cancelar</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                <tr>
                    <td>{{$order->id}}</td>
                    <td>@if(isset($order->created_at)) {{ Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i')}} @endif</td>
                    <td>{{$order->concept->name}}</td>
                    <td>{{$order->orderAuthor()->name}}</td>
                    <td>
                        @if($order->status == 'in progress')
                        <a href="{{route('orderPreview', $order->id)}}">{{$order->status}}</a>
                        @else
                        {{$order->status}}
                        @endif
                    </td>
                    <td>${{number_format($order->total, 0,',','.')}}</td>
                        <td @if(!$order->document_link)
                                style="visibility: hidden"
                            @endif >
                            <a class="uk-button uk-button-default" uk-tooltip="Ver orden" href="{{$order->document_link}}"><span uk-icon="icon: file-pdf"></span></a></td>
                        </td>
                    <td @if($order->status == 'completed' || $order->status == 'in progress' || $order->status == 'cancelled' ) 
                            style="visibility: hidden"
                        @endif >
                        <a href="#complete{{$order->id}}" class="uk-button uk-button-default" uk-tooltip="Completar" uk-toggle><span uk-icon="icon: check"></span></a>
                    </td>
                    <td @if($order->status == 'pending')
                        style="visibility: visible"
                        @else
                        style="visibility: hidden"
                        @endif >
                        <a href="#cancel{{$order->id}}" class="uk-button uk-button-default" uk-tooltip="Cancelar" uk-toggle><span uk-icon="icon: close"></span></a>
                    </td>
                </tr>

                @include('partials.confirms.completeOrder',['url'=>"/orderToCompleted/{$order->id}", 'message'=>"Seguro quieres completar la orden #{$order->id}? El stock de cada item se descontará del local elegido", 'id' => $order->id])
                @include('partials.confirms.cancelOrder',['url'=>"/orderToCancelled/{$order->id}", 'message'=>"Seguro quieres cancelar la orden #{$order->id}? No podrás volver a cambiar su estado", 'id' => $order->id])
                @empty
                <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> No Existen ordenes actualmente.</h3>
                @endforelse

            </tbody>
        </table>
        {{ $orders->appends($_GET)->links() }}

    </div>
</div>

@endsection
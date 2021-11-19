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
    <h4 class=" uk-heading-line uk-text-center uk-margin-top"> <span>Listado de ordenes</span></h4>

    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Creación</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>PDF</th>
                    <th>Completar</th>
                    <th>Cancelar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>{{$order->id}}</td>
                    <td>{{$order->created_at->isoFormat('DD-MM-YYYY [a las] hh:mm')}}</td>
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
                    <td @if($order->status == 'completed' || $order->status == 'in progress') 
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


                @endforeach

            </tbody>
        </table>
    </div>
</div>

@endsection
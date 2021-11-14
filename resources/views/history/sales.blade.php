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
    <table class="uk-table uk-table-divider uk-margin-bottom">
        <thead>
            <tr>
                <th>ID</th>
                <th>Creación</th>
                <th>Estado</th>
                <th>Total</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{$order->id}}</td>
                <td>{{$order->created_at->isoFormat('DD-MM-YYYY [a las] hh:mm')}}</td>
                <td>{{$order->status}}</td>
                <td>$ {{$order->total}}</td>
                @if($order->document_link)
                    <td>
                        <a href="{{$order->document_link}}" uk-icon="icon: file-pdf">Descargar &nbsp;</a>
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

@php
    use App\Models\Warehouse;
@endphp

<div class="uk-container primer-div">
    <h1 class="uk-heading-divider">Gestión de depósitos</h1>

    <div class="uk-child-width-1-2@s uk-grid-match uk-margin" uk-grid>
        @foreach ($warehouses as $warehouse)
        <div>
          <a href="{{route('warehouseStock', ['warehouseSlug' => $warehouse->slug])}}">
            <div style='cursor: pointer;' class="warehouse-card uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
                <h3 class="uk-card-title">@if($warehouse->type == 'storage')<i class="fas fa-warehouse icon"></i>@else <i class="fas fa-store-alt"></i></i>@endif  {{$warehouse->name}}</h3>
                <p>Ubicado en: {{$warehouse->address}}.</p>
                <a href="{{route('warehouseStock', ['warehouseSlug' => $warehouse->slug])}}" class="uk-link-heading"><i class="fas fa-list-alt"></i> Listado ({{count(Warehouse::getProducts($warehouse->id))}} variedad/es en total)</a>
            </div>
          </a>
        </div>
        @endforeach

        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark warehouse-card--add" style="cursor:pointer;" onclick="sendToCreateWarehouse()">
                <h3 class="uk-card-title"><i class="fas fa-plus-circle"></i> Agregar/editar depósito</h3>
                <p>Haga click en el panel para agregar un depósito nuevo.</p>
            </div>
        </div>

    </div>

</div>

<script>
    const sendToCreateWarehouse = () => {
        window.location.pathname = '/warehouses/edit'
    }
</script>
@endsection

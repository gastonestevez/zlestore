@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">
    <h1 class="uk-heading-divider">Gestión de depósitos</h1>
    
    <div class="uk-child-width-1-2@s uk-grid-match uk-margin" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
                <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> Saint John</h3>
                <p>Ubicado en: Nazca 2900 y cuenta con 90 productos. </p>
                <a href="{{url('/warehouse/search/1')}}" class="uk-link-heading"><i class="fas fa-list-alt"></i> Listado</a>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
                <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> Black River</h3>
                <p>Ubicado en: Nazca 2900, cuenta con 90 variedades de productos. </p>
                <a href="{{url('/warehouse/search/1')}}" class="uk-link-heading"><i class="fas fa-list-alt"></i> Listado</a>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark">
                <h3 class="uk-card-title"><i class="fas fa-warehouse icon"></i> Hurlingham</h3>
                <p>Ubicado en: Nazca 2900, cuenta con 90 variedad de productos. </p>
                <a href="{{url('/warehouse/search/1')}}" class="uk-link-heading"><i class="fas fa-list-alt"></i> Listado</a>

            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-dark warehouse-card--add" onclick="sendToCreateWarehouse()">
                <h3 class="uk-card-title"><i class="fas fa-plus-circle"></i> Agregar depósito</h3>
                <p>Haga click en el panel para agregar un depósito nuevo.</p>
            </div>
        </div>
        
    </div>

</div>

<script>
    const sendToCreateWarehouse = () => {
        window.location.pathname = '/warehouse/create'
    }
</script>
@endsection
@extends('layouts.app')

@section('main')

<div class="uk-container">
    <h1 class="uk-text-lead">Notificaciones</h1>
    <p class="uk-text-normal">Notificaciones del día de hoy</p>

    <table class="uk-table uk-table-hover uk-table-divider">
        <thead>
            <tr>
                <th>Producto</th>
                <th>SKU</th>
                <th>Nuevo stock</th>
                <th>Distribuir Stock</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Mandarinas</td>
                <td>128ACNB038</td>
                <td>-20</td>
                <td>
                    <a href="" uk-icon="icon: git-fork"> </a>
                    <a href="" class="uk-link-heading">Distribuir</a>
                </td>
            </tr>
            <tr>
                <td>Bananas</td>
                <td>AJS9800X</td>
                <td>-180</td>
                <td>
                    <a href="" uk-icon="icon: git-fork"> </a>
                    <a href="" class="uk-link-heading">Distribuir</a>
                </td>
            </tr>
            <tr>
                <td>Papas</td>
                <td>P0T4T03SX98098</td>
                <td>-5</td>
                <td>
                    <a href="" uk-icon="icon: git-fork"> </a>
                    <a href="" class="uk-link-heading">Distribuir</a>
                </td>
            </tr>
        </tbody>
    </table>

</div>



@endsection

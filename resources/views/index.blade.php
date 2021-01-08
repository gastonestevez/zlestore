@extends('layouts.layout')
@section('titulo')
ZLE - Control de Stock
@endsection
@section('main')

<div class="uk-container primer-div">

  <h1 class="uk-heading-divider">Lista de Pedidos</h1>


    <button class="uk-button uk-button-secondary uk-margin">ACTUALIZAR LISTA</button>

    <div class="uk-flex">

      <div class="uk-margin pr">
        <form class="uk-search uk-search-default">
          <span class="uk-search-icon-flip" uk-search-icon></span>
          <input class="uk-search-input" type="search" placeholder="SKU ...">
        </form>
      </div>

      <form>
        <select class="uk-select">
          <option>Estado</option>
          <option>Pendientes</option>
          <option>Completados</option>
        </select>
      </form>

    </div>

  <div class="uk-overflow-auto">

    <table class="uk-table uk-table-striped uk-table-hover">
      <thead>
          <tr>
              <th>N° Orden</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Cliente</th>
              <th>SKU</th>
              <th>Producto</th>
              <th>Unidades</th>
              <th>Cajas</th>
              <th>Gestionar</th>
              <th>Total</th>
              <th></th>
          </tr>
      </thead>
      <tbody>
          <tr>
              <td>#1</td>
              <td>12/2/19</td>
              <td>Pendiente</td>
              <td>Gasalla</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td><button class="uk-button uk-button-default" type="button">Preparar</button></td>
              <td>$5.000</td>
              <td><a href="" uk-icon="icon: close"></a></td>
          </tr>
          <tr class="item">
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>RT-1831</td>
              <td>Manguera</td>
              <td>50</td>
              <td>N/D</td>
              <td></td>
              <td></td>
              <td></td>
          </tr>
          <tr class="item">
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>R5-871</td>
              <td>Bombucha</td>
              <td>300</td>
              <td>1</td>
              <td></td>
              <td></td>
              <td></td>
          </tr>
          <tr class="item">
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>ZX-12</td>
              <td>Globos</td>
              <td>1000</td>
              <td>4</td>
              <td></td>
              <td></td>
              <td></td>
          </tr>
          <tr>
              <td>#2</td>
              <td>4/12/19</td>
              <td>Pendiente</td>
              <td>Stallone</td>
              <td>SHA-1114</td>
              <td>Porta retratos</td>
              <td>100</td>
              <td>1</td>
              <td><button class="uk-button uk-button-default" type="button">Preparar</button></td>
              <td>$10.000</td>
              <td><a href="" uk-icon="icon: close"></a></td>
          </tr>
          <tr>
              <td>#3</td>
              <td>3/5/20</td>
              <td>Pendiente</td>
              <td>Franco de Vita</td>
              <td>LRTA-3232</td>
              <td>Candelabro</td>
              <td>1500</td>
              <td>3</td>
              <td><button class="uk-button uk-button-default" type="button">Preparar</button></td>
              <td>$50.000</td>
              <td><a href="" uk-icon="icon: close"></a></td>
          </tr>
      </tbody>
    </table>

  </div>

</div>

@endsection

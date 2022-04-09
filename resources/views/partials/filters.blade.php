<div class="uk-flex">

    <form class="searchForm uk-search uk-search-default" method="get">
      <div class="pr uk-margin-bottom">
          <input value="{{old('id', $request->id)}}" class="uk-search-input" type="search" placeholder="ID ..." name="id">
      </div>
      <div class="pr uk-margin-bottom">
          <input value="{{old('sku', $request->sku)}}" class="uk-search-input" type="search" placeholder="SKU ..." name="sku">
      </div>
      <div class="pr uk-margin-bottom">
          <input value="{{old('name', $request->name)}}" class="uk-search-input" type="search" placeholder="Nombre ..." name="name">
      </div>
      <div class="pr uk-margin-bottom">
        <input value="{{old('name', $request->price)}}" class="uk-search-input" type="search" placeholder="Precio ..." name="price">
      </div>
      <button class="uk-button uk-button-default limpiar-busqueda" style="margin-right: 15px; margin-bottom: 15px;">Buscar</button>
      <div class="pr uk-margin-bottom">
        <label for="limpiar" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Limpiar Búsqueda</label>
      </div>
      
      <button onclick="clearCart()" for="limpiarCarrito" class="uk-button uk-button-default limpiar-busqueda" style="min-width: 168px;">Vaciar Carrito</button>
    </form>

    <form class="uk-search uk-search-default" style="pointer-events: none;" method="get">
      <button id='limpiar' hidden class="uk-button uk-button-default limpiar-busqueda">Limpiar Búsqueda</button>
    </form>

  </div>
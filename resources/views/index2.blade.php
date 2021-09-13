@foreach ($products as $product)
    <b>ID </b>{{$product->id}}<br>
    <b>Titulo </b>{{$product->name}}<br>
    <b>SKU </b>{{$product->sku}}<br>
     <b>Precio </b> ${{number_format($product->price, 0, ',','.')}}<br>
     <b>Unidades por caja </b> {{$product->units_in_box}}<br><br>
@endforeach
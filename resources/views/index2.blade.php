@foreach ($products as $product)
    {{$product->post_title}} {{$product->sku}}<br>
@endforeach
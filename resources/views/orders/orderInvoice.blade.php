<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ZLE - PDF orden #{{$order->id}}</title>
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Nunito", sans-serif;
    }
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        border: 1px solid #ddd;
        text-align: left;
        padding: 8px;
    }
    tr:nth-child(even) {
        background-color: #eee;
    }
    body{
        margin-top: 32px;
        padding: 0 32px; 
    }
    .header {
    }
    /* .header .header__nav-pic__container {
        display: inline-block;
        width: 100px;
    } */

    .header .header__nav-pic {
        display: inline-block;
        width: 100px;
    }
    .header h4 {
        display: inline-block;
        float: right;
        margin: revert;
    }
    main {
        margin-top: 22px;
    }
    .main-table table {
        margin-top: 10px;
    }
    .main-total {
        width: 100%;
        margin-top: 10px;
    }
    .main-notes {
        margin-top: 32px;
    }

    .total {
        font-size: 18px;
        border: 2px solid rgb(0, 0, 0);
        padding: 10px;
     display: inline-block;
    }
</style>
<body>
    <header class="header">
        <img class="header__nav-pic" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/LogoZLESTORE.png'))) }}" alt="Logo ZLE" />
        <h4>{{ Carbon\Carbon::parse($order->created_at)->format('d-m-Y H:i')}}</h4>
    </header>
    <main>
        <div class="main-table">
            <h4>Presupuesto orden #{{$order->id}}</h4>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>SKU</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                </tr>
                
                @foreach ($order->orderItems() as $item)
                <tr>
                    <td>{{$item->product_id}}</td>
                    <td>{{$item->product_name}}</td>
                    <td>{{$item->product_sku}} </td>
                    <td>{{$item->quantity}}</td>
                    <td>${{number_format($item->price, 0,',','.')}}</td>
                </tr>
                @endforeach
            </table>
        </div>
        <div class="main-total">
            @if ($request->category_discount)
                @foreach ($request->category_discount as $index => $category_discount)
                    @if ($category_discount == "all")
                    <h4>
                        Se realizó un descuento del {{$request->discount[$index]}}% en el total de la compra
                    </h4> 
                    @else   
                    <h4>
                        Se realizó un descuento del {{$request->discount[$index]}}% en la categoría {{$category_discount}}
                    </h4>       
                    @endif
                @endforeach
            @endif
            <h4 class="total">
                Total: ${{number_format($order->total, 0,',','.')}}
            </h4>
        </div>
        @if($request->info)
            <div class="main-notes">
                <h4>Notas:</h4>
                <p>
                    {{$request->info}}
                </p>
            </div>
        @endif
    </main>
</body>
</html>
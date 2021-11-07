<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ZLE - PDF</title>
</head>
<body>

    @foreach ($order->orderItems() as $item)
        {{$item->product_name}} <br>
    @endforeach

    <br><br><br>

    {{$request->info}}
</body>
</html>
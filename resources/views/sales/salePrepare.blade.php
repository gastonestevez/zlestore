<style>
    .producto {
        height: 100px;
        background-color: beige;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px;
        cursor: pointer;
    }

    .producto:hover {
        background-color: #d3d3c8
    }

    .lista {
        border: 2px solid red;
        border-radius: 50px;
        padding: 15px;
        margin: 10px;
        width: fit-content;
    }

    .remove {
        cursor: pointer;
        margin-left: 10px;
        font-size: 12px; 
        color: red;
        border: 1px solid black;
        border-radius: 50px;
        padding: 5px;
    }

    .continuar {
        height: 50px;;
        display: block;
        margin: 50;
        cursor: pointer;
    }
</style>

<h1>Selecciona los productos</h1>

<div class='producto' id={{$producto1->id}}>
    Nombre: {{$producto1->name}}
    <br>
    Precio: ${{number_format($producto1->price, 0, ',','.')}}
    <br>
    SKU: RTAD-34
    <br>
    Stock total: 255
    <br>
    Stock en local: 57
    <br>
    <input type="number" value="23">
</div>

<div class='producto' id={{$producto2->id}}>
    {{$producto2->name}}
    <br>
    Precio: ${{number_format($producto2->price, 0, ',','.')}}
    <br>
    SKU:ASD-1004
    <br>
    Stock total: 103
    <br>
    Stock en local: 57
    <input type="number" value="0">
</div>

<div class='producto' id={{$producto3->id}}>
    {{$producto3->name}}
    <br>
    Precio: ${{number_format($producto3->price, 0, ',','.')}}
    <br>
    SKU:RTK-222
    <br>
    Stock total: 15
    <br>
    Stock en local: 15
    <input type="number" value="10">
</div>

<h1>Tu pedido</h1>

<form action="">
    <div style='display:flex;'>
        <div class='lista'>
            <span>Cortina 384 Leds - Blanco, Por bulto - 10</span>
            <input type="hidden" value="Cortina 384 Leds - Blanco, Por bulto">
            <label class='remove' for="">X</label>
        </div>
        <div class='lista'>
            <span>144 Leds Red - Blanco, Por caja - 23</span>
            <input id="" type="hidden" value="144 Leds Red - Blanco, Por caja">
            <label class='remove' for="">X</label>
        </div>   
    </div>
    <a class='continuar' href="/saleConfirm">Continuar con el pedido</a>
</form>

<script>
    
</script>
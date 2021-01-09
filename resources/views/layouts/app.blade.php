<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ZLE - WAREHOUSE</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- UIkit CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.6.3/dist/css/uikit.min.css" />

    <!-- UIkit JS -->
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.6.3/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.6.3/dist/js/uikit-icons.min.js"></script>
</head>
<body>
<header>
    <nav class="uk-navbar-container" uk-navbar>
        <div class="uk-navbar-left">
            <ul class="uk-navbar-nav">
                <li><a href="#">Depósitos</a></li>
                <li><a href="#">Productos</a></li>
                
            </ul>
    
        </div>
        <div class="uk-navbar-center">
            <a class="uk-navbar-item uk-logo" href="#">Z L E</a>
        </div>
        <div class="uk-navbar-right">
    
            <ul class="uk-navbar-nav">
                <li><a href="#">
                    <span class="uk-icon uk-margin-small-right" uk-icon="icon: future"></span>
                    Sincronizar</a></li>
                
                <li><a href="#">Login</a></li>
                
            </ul>
        </div>
    </nav>
</header>

<main class="uk-section uk-section">
    @yield('main')
</main>

<footer>

</footer>

</body>
</html>
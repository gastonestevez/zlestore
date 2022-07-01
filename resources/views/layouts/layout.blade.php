<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no">
    <meta name="Description" content="Sistema de stock para ZLE Store.">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sticky-table-headers"></script>

    @include('partials.links')

    <title>@yield('titulo')</title>
    {{-- <link rel="stylesheet" href="/css/@yield('css').css">  Para agregar un css a una vista en particular --}}
    @yield('scripts') {{-- Para agregar JS a una vista en particular --}}

  </head>
  <body>


    <header>

      {{-- Navbar --}}

      @include('partials.navbar')

    </header>


    <main id="main">

      {{-- Notificaciones --}}
      @if (session('success'))
        <div class="wow animated fadeInDown alert sticky-notification notification-success">
         {{session('success')}}
        </div>
      @endif

      @if (session('error'))
        <div class="wow animated fadeInDown alert sticky-notification notification-error">
          {{session('error')}}
        </div>
      @endif


      @yield('main')


    </main>

    <footer>

      {{-- Footer --}}

      @include('partials.footer')

    </footer>

    {{-- Social Bar --}}

    {{--  @include('partials.socialbar') --}}



    {{-- Scripts --}}

    @include('partials.scripts')

  </body>
</html>

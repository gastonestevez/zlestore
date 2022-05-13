{{-- uk-hidden@m = Oculto salvo en M y menores --}}
{{-- uk-visible@m = Visible salvo en m y menores --}}


{{-- Sticky nav, desktop --}}
<div class="uk-visible@m" uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky;">
    <img src="" alt="">
    <div class="uk-position-top">
        <nav class="uk-navbar-container uk-navbar-transparent subtle-background pl pr" uk-navbar>
            <div class="uk-navbar-left">

              <a class="uk-navbar-item uk-logo" href="{{route('home')}}"><img class="logo-nav" src="/img/LogoZLESTORE.png" alt="Logo ZLE" /></a>
            @if (Auth::user())

                <ul class="uk-navbar-nav">
                  <li>
                      <a class="uk-text-bold" href="{{route('home')}}">Inicio</a>
                  </li>
                  <li>
                      <a class="uk-text-bold" href="#">Pedidos</a>
                        <div class="uk-navbar-dropdown">
                          <ul class="uk-nav uk-navbar-dropdown-nav">
                            <li><a class="uk-text" href="/wcOrders">Ver pedidos online</a></li>
                            <li><a class="uk-text" href="{{route('createOrder')}}">Armar pedido offline</a></li>
                            @if ($orderInProgress && $id)
                              <li><a class="uk-text" href="{{route('orderPreview', $id)}}">Continuar pedido offline</a></li>
                            @endif
                          </ul>
                        </div>
                  </li>
                    <li>
                        <a class="uk-text-bold" href="#">Stock</a>
                        <div class="uk-navbar-dropdown">
                            <ul class="uk-nav uk-navbar-dropdown-nav">
                                <li><a href="{{route('stockList')}}">Stock general</a></li>
                                <li class="uk-nav-divider"></li>
                                @foreach ($warehouses as $warehouse)
                                    <li><a href="{{route('warehouseStock', $warehouse->slug)}}">{{$warehouse->name}}</a></li></li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a class="uk-text-bold" href="{{route('warehouses')}}">Depósitos</a>
                    </li>
                    <li>
                      <a class="uk-text-bold" href="#">Historial</a>
                      <div class="uk-navbar-dropdown">
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                          <li><a href="{{route('historySales')}}">Ventas offline</a></li>                    
                          <li><a style="cursor:not-allowed" href="#">Ventas online</a></li>                    
                          <li><a class="text-nowrap" href="{{route('historyMovements')}}">Movimientos de stock</a></li>                    
                        </ul>
                    </div>
                  </li>
                </ul>
            </div>

            <div class="uk-navbar-right">


                <ul class="uk-navbar-nav">
                    <li>
                      <a href="#">
                        <span class="uk-icon uk-margin-small-right" uk-icon="icon: user"></span>
                        {{Auth::user()->name}}
                        <span class="uk-label uk-margin-left"
                        style="background: @if(Auth::user()->role == 'admin') purple @elseif(Auth::user()->role == 'employee')  green @endif; font-size: 10px;">{{Auth::user()->role}}</span>
                      </a>
                      <div class="uk-navbar-dropdown">
                          <ul class="uk-nav uk-navbar-dropdown-nav">
                            @if (Auth::user())
                              <li><a href="{{route('profile')}}">Mi perfil</a></li>
                            @endif
                            @if (Auth::user()->role == 'admin')
                              <li><a href="{{route('users')}}">Gestionar usuarios</a></li>
                            @endif
                              {{-- <li class="uk-nav-header">Header</li> --}}
                              <li class="uk-nav-divider"></li>
                              <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                  @csrf
                                  <button style="background: none;border: none;color: #999;">Salir</button>
                                </form>
                              </li>
                          </ul>
                      </div>
                    </li>
                </ul>

            </div>
          @endif
        </nav>
    </div>
</div>


{{-- Sticky nav mobile y tablet --}}
<div class="uk-hidden@m" uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky">
  <nav class="uk-navbar uk-navbar-container uk-navbar-transparent subtle-background" uk-navbar>
      <div class="uk-navbar-left">
        <a class="uk-navbar-item uk-logo" href="{{route('home')}}"><img class="logo-nav" src="/img/LogoZLESTORE.png" alt="Logo ZLE" /></a>
      </div>
      <div class="uk-navbar-right">
        <ul class="uk-hidden@m uk-navbar-nav uk-nav-parent-icon">
          <li class="uk-visible-small">
            <a class="burger" href="#navbarMobile"  uk-navbar-toggle-icon uk-toggle uk-toggle="target: #offcanvas-slide"></a>
          </li>
        </ul>
      </div>
  </nav>
</div>

<div id="navbarMobile" uk-offcanvas="mode: slide; overlay: true">
  <div class="uk-offcanvas-bar">

    <button class="uk-offcanvas-close" type="button" uk-close></button>

    <div class="uk-navbar-left">

    @if (Auth::user())
      <ul class="uk-nav uk-nav-offcanvas uk-nav-center uk-nav-parent-icon uk-text-left" style="transform: translateY(50%);" uk-nav="multiple: true">
        {{-- Para que al clickear un link no se cierre el offcanvas hay que agregarle a la etiqueta A el atributo uk-scroll --}}
        <li><a onclick="UIkit.offcanvas('#navbarMobile').hide();" href="{{route('home')}}" uk-scroll>Inicio</a></li>
        <li class="uk-parent">
          <a href="#">Pedidos</a>
          <ul class="uk-nav-sub">
            <li><a class="uk-text" href="/wcOrders">Ver pedidos online</a></li>
            <li><a class="uk-text" href="{{route('createOrder')}}">Armar pedido offline</a></li>
            @if ($orderInProgress && $id)
              <li><a class="uk-text" href="{{route('orderPreview', $id)}}">Continuar pedido offline</a></li>
            @endif
          </ul>
        <li><a onclick="UIkit.offcanvas('#navbarMobile').hide();" href="/wcOrders" uk-scroll>Pedidos</a></li>
        <li class="uk-parent">
          <a href="#">Stock</a>
          @if (Auth::user()->role == 'admin')
              <ul class="uk-nav-sub">
                  <li><a href="{{route('stockList')}}">Stock general</a></li>
                  <li class="uk-nav-divider"></li>
                  @foreach ($warehouses as $warehouse)
                      <li><a href="{{route('warehouseStock', $warehouse->slug)}}">{{$warehouse->name}}</a></li></li>
                  @endforeach
              </ul>
          @endif
        </li>
        <li>
          <a href="{{route('warehouses')}}">Depósitos</a>
        </li>
          
        <li class="uk-parent">
          <a href="#">Historial</a>
          <ul class="uk-nav-sub">
            <li><a href="{{route('showProfile', Auth::user()->id)}}">Ventas offline</a></li>
            <li><a style="cursor:not-allowed" href="#">Ventas online</a></li>
            <li><a href="{{route('historyMovements')}}">Movimientos de stock</a></li>

          </ul>
        </li>

        <hr class="uk-divider-small">

        <li class="uk-parent">
          <a href="#">
            <span class="uk-icon uk-margin-small-right" uk-icon="icon: user"></span>
            {{Auth::user()->name}}
          </a>

          <ul class="uk-nav-sub">
            @if (Auth::user())
              <li><a href="{{route('showProfile', Auth::user()->id)}}">Mi perfil</a></li>
            @endif
              <li><a href="{{route('users')}}">Gestionar usuarios</a></li>
              {{-- <li class="uk-nav-header">Header</li> --}}
              <li class="uk-nav-divider"></li>
              <li>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button style="background: none;border: none;color: #999;">Salir</button>
                </form>
              </li>
          </ul>
        </li>

      </ul>
    @endif

  </div>
</div>

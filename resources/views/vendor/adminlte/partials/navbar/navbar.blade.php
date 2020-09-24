<nav class="main-header navbar
    {{ $layoutHelper->getConfig('classes_topnav_nav', 'navbar-expand') }}
    {{ $layoutHelper->getConfig('classes_topnav', 'navbar-white navbar-light') }}">


    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $layoutHelper->getConfig('navbar-left',[]), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $layoutHelper->getConfig('navbar-right',[]), 'item')

        {{-- User menu link --}}
        @if(Auth::user())
            @if($layoutHelper->getConfig('usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if($layoutHelper->getConfig('right_sidebar'))
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>

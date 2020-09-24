@extends('adminlte::master',['layoutBuild'=>isset($layoutBuild)?$layoutBuild:[] ] )
@php 

    $layoutHelper=new  App\Providers\Layout(isset($layoutBuild)?$layoutBuild:[]);
@endphp

@if($layoutHelper->isLayoutTopnavEnabled())
    @php( $def_container_class = 'container' )
@else
    @php( $def_container_class = 'container-fluid' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">
       


        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        <div class="content-wrapper {{ $layoutHelper->getConfig('classes_content_wrapper') ?? '' }}">

            {{-- Content Header --}}
            <div class="content-header">
                <div class="{{ $layoutHelper->getConfig('classes_content_header') ?: $def_container_class }}">
                    @isset($page_block)
                    <div class="card  bg-danger">
                        <div class="card-body">
                            <h4>PAGE BLOCK - <span class="text-capitalize">Sementara Halaman ini belum dapat diopresikan</span></h4>
                            <small>Mohon menghubungi administrator untuk pengunaan halaman ini</small>
                        </div>
                    </div>
                    @endisset

                    @yield('content_header')
                </div>
            </div>

            {{-- Main Content --}}
            <div class="content">
                <div class="{{ $layoutHelper->getConfig('classes_content','')?? $def_container_class }}">
                    @yield('content')
                </div>
            </div>

        </div>

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if($layoutHelper->getConfig('right_sidebar'))
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop
<style type="text/css">
    table {
        font-size: 11px;
    }


</style>



@section('adminlte_js')
    @stack('js')
    @yield('js')

     <style type="text/css">


        .table-fix .table{
            border-collapse: separate;
            position: relative;
        }
       

        .table-fix{
            max-height: 80vh!important;
            overflow: scroll;
            padding: 0px;
        }
        </style>
        <script type="text/javascript">
            
        var $th = $('.table-fix').find('thead');
        $('.table-fix').on('scroll', function() {
            $(this).find('thead').css('transform', 'translateY('+ (this.scrollTop-2)+'px)' );

        });
        </script>

@stop



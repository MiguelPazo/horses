<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistema de Calificación - Inicio</title>

    <meta name="description" content="Source code generated using layoutit.com">
    <meta name="author" content="LayoutIt!">

    <link rel="stylesheet" href="{{ asset('/js/libs/bootstrap/dist/css/bootstrap.min.css') }}">

    @yield('css')

    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/print.css') }}" media="print">

    <script>
        var BASE_URL = '{{ asset('/')}}';
    </script>
</head>
<body>

<div class="container-fluid">
    @include('_partials.popup')

    <div class="row">
        <div class="col-md-12">
            @if(Auth::check())
                <a href="{{ url('/auth/logout') }}" type="button" class="btn btn-danger btn_exit">
                    <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Salir
                </a>
                <a href="{{ route('admin.dashboard') }}" role="button" class="btn btn-default btn_exit btn_home">
                    <span class="glyphicon glyphicon-home"></span>
                </a>
                <a href="{{ Request::url() }}" role="button" class="btn btn-default btn_exit btn_refresh">
                    <span class="glyphicon glyphicon-refresh"></span>
                </a>
            @endif

            @yield('content')

        </div>
    </div>
</div>
<script src="{{ asset('/js/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('/js/libs/jquery-numeric/dist/jquery-numeric.js') }}"></script>
<script src="{{ asset('/js/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/js/app/main.js') }}"></script>
@yield('scripts')
<script src="{{ asset('/js/libs/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>
<script src="{{ asset('/js/app/analytics.js') }}"></script>
</body>
</html>
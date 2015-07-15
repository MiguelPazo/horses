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
    <link rel="stylesheet" href="{{ asset('/js/libs/jqueryui/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <script src="{{ asset('/js/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/libs/jqueryui/jquery-ui.js') }}"></script>
    <script src="{{ asset('/js/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</head>
<body>

<div class="container-fluid">
    @yield('content')
</div>

</body>
</html>
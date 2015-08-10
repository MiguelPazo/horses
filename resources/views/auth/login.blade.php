@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Sistema de Calificación
    </h3>

    {!! Form::open(array('url' => 'auth/login', 'id'=>'formLogin')) !!}
    <div class="form-group">
        <label for="user">
            Usuario:
        </label>
        <input type="text" class="form-control" name="user" id="user"/>
    </div>
    <div class="form-group">
        <label for="password">
            Contraseña:
        </label>
        <input type="password" class="form-control" name="password" id="password"/>
    </div>
    <button type="submit" class="btn btn-default">
        Ingresar
    </button>
    {!! Form::close() !!}

    <script src="{{ asset('/js/login.js') }}"></script>
@endsection

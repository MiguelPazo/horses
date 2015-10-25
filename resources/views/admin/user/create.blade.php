@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Nuevo Usuario
    </h3>

    <div class="panel-body">
        {!! Form::open(['route' => 'admin.user.store', 'class' => 'formValid']) !!}

        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('admin.user.index') }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('admin.user._partials.fields')
        {!! Form::close() !!}
    </div>
    <script src="{{ asset('/js/app/admin/user.js') }}"></script>
@endsection

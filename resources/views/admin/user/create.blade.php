@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Nuevo Usuario
    </h3>

    <div class="panel-body">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {!! Form::open(['route' => 'admin.user.store']) !!}
        @include('admin.user.partials.fields')
        {!! Form::submit('Guardar', ['class' => 'btn btn-default']) !!}
        <a href="{{ route('admin.user.index') }}"
           class="btn btn-danger">Cancelar</a>

        {!! Form::close() !!}
    </div>
    <script src="{{ asset('/js/admin.js') }}"></script>
@endsection

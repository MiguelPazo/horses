@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Nuevo Concurso
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

        {!! Form::open(['route' => 'admin.tournament.store']) !!}

        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('admin.tournament.index') }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('admin.tournament._partials.fields')
        {!! Form::close() !!}
    </div>
    <script src="{{ asset('/js/admin.js') }}"></script>
@endsection

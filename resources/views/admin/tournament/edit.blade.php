@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Editar Concurso
    </h3>

    <div class="panel-body">
        {!! Form::open(['route' => ['admin.tournament.update', $oTournament->id], 'method' => 'PUT', 'class' => 'formValid']) !!}

        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('admin.tournament.index') }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('admin.tournament._partials.fields')

        {!! Form::close() !!}
    </div>
@endsection

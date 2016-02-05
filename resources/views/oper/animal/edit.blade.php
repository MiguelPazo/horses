@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Nuevo Animal
    </h3>

    <div class="panel-body">
        {!! Form::open(['route' => ['oper.animal.update', $oAnimal->id], 'method' => 'PUT', 'class' => 'formValid']) !!}

        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('oper.animal.index') }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('oper.animal._partials.fields')
        {!! Form::close() !!}
    </div>
@endsection

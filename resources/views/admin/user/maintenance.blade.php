@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        {{ $title }}
    </h3>

    <div class="panel-body">
        {!! Form::open($formHeader) !!}

        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('admin.user.index') }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('admin.user._partials.fields')
        {!! Form::close() !!}
    </div>
@endsection
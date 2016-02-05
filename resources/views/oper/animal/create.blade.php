@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Nuevo Animal
    </h3>

    <div class="panel-body">
        {!! Form::open(['route' => 'oper.animal.store', 'class' => 'formValid']) !!}

        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('oper.animal.index') }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('oper.animal._partials.fields')
        {!! Form::close() !!}
    </div>
    <script src="{{ asset('/js/libs/bootstrap-multiselect/dist/js/bootstrap-multiselect-collapsible-groups.js') }}"></script>
    <script src="{{ asset('/js/libs/bootstrap-multiselect/tests/spec/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('/js/app/oper/animal.js') }}"></script>
@endsection

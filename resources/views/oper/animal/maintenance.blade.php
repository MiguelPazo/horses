@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('/js/libs/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}"/>
    <link rel="stylesheet" href="{{ asset('/js/libs/jquery-ui/themes/base/jquery-ui.min.css') }}">
@endsection

@section('content')
    <h3 class="text-center text-primary">
        {{ $title }}
    </h3>

    <div class="panel-body">
        {!! Form::open($formHeader) !!}

        {!! Form::submit('Guardar', ['class' => 'btn btn-success btn_disable']) !!}
        <a href="{{ route('oper.animal.index') }}" class="btn btn-danger btn_disable">Cancelar</a>

        <p></p>
        @include('oper.animal._partials.fields')
        {!! Form::close() !!}
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/libs/jquery-ui/ui/minified/i18n/datepicker-es.min.js') }}"></script>
    <script src="{{ asset('/js/libs/devbridge-autocomplete/dist/jquery.autocomplete.min.js') }}"></script>
    <script src="{{ asset('/js/app/oper/animal/maintenance_common.js') }}"></script>
    <script src="{{ asset('/js/app/oper/animal/maintenance.js') }}"></script>
@endsection
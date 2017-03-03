@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('/js/libs/jquery-ui/themes/base/jquery-ui.min.css') }}">
@endsection

@section('content')
    <h3 class="text-center text-primary">
        {{ $title }}
    </h3>

    <div class="panel-body">
        {!! Form::open($formHeader) !!}

        {!! Form::submit('Guardar', ['class' => 'btn btn-success btn_disable']) !!}
        <a href="{{ route('admin.tournament.index') }}"
           class="btn btn-danger btn_disable">Cancelar</a>

        <p></p>
        @include('admin.tournament._partials.fields')
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/libs/jquery-ui/ui/minified/i18n/datepicker-es.js') }}"></script>
    <script src="{{ asset('/js/app/admin/tournament.js') }}"></script>
@endsection
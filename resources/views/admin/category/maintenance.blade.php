@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        {{ $title }}
    </h3>

    <div class="panel-body">
        {!! Form::open($formHeader) !!}
        {!! Form::submit('Guardar', ['class' => 'btn btn-success btn_disable']) !!}
        <a href="{{ route('admin.tournament.category', $oTournament->id) }}"
           class="btn btn-danger btn_disable">Cancelar</a>

        <p></p>
        @include('admin.category._partials.fields')
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/app/admin/category.js') }}"></script>
    <script src="{{ asset('/js/libs/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>
@endsection

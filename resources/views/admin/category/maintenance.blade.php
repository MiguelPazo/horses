@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        {{ $title }}
    </h3>

    <div class="panel-body">
        {!! Form::open($formHeader) !!}
        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('admin.tournament.category', $oTournament->id) }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('admin.category._partials.fields')
        {!! Form::close() !!}
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/app/admin/category.js') }}"></script>
@endsection

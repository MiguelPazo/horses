@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Editar Categoría de {{ $oTournament->description }}
    </h3>

    <div class="panel-body">

        {!! Form::open(['url' => ['/admin/category/update', $oCategory->id], 'method' =>'PUT', 'class' => 'formValid'])
        !!}
        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('admin.tournament.category', $oCategory->tournament_id) }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('admin.category._partials.fields')
        {!! Form::close() !!}
    </div>

    <script src="{{ asset('/js/admin/category.js') }}"></script>
@endsection

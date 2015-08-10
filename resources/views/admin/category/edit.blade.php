@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Editar Categoría de {{ $oTournament->description }}
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

        {!! Form::open(['url' => ['/admin/category/update', $oCategory->id], 'method' =>
        'PUT', 'id' => 'formCategory']) !!}
        {!! Form::submit('Guardar', ['class' => 'btn btn-default']) !!}
        <a href="{{ route('admin.tournament.category', $oCategory->tournament_id) }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('admin.category.partials.fields')
        {!! Form::close() !!}
    </div>

    <script src="{{ asset('/js/admin.js') }}"></script>
@endsection

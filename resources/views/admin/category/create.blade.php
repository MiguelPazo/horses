@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Nueva Categoría para {{ $oTournament->description }}
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

        {!! Form::open(['url' => ['/admin/category/store', $oTournament->id ], 'id' => 'form']) !!}
        {!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
        <a href="{{ route('admin.tournament.category', $oTournament->id) }}"
           class="btn btn-danger">Cancelar</a>

        <p></p>
        @include('admin.category._partials.fields')
        {!! Form::close() !!}
    </div>

    <script src="{{ asset('/js/admin.js') }}"></script>
@endsection

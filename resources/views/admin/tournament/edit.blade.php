@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center text-primary">
                Editar Torneo
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

                {!! Form::open(['route' => ['admin.tournament.update', $oTournament->id], 'method' => 'PUT']) !!}
                @include('admin.tournament.partials.fields')
                {!! Form::submit('Guardar', ['class' => 'btn btn-default']) !!}

                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script src="{{ asset('/js/admin.js') }}"></script>
@endsection

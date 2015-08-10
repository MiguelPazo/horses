@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Dashboard
    </h3>

    <div class="panel-body">
        <a href="{{ route('admin.tournament.index') }}" role="button" class="btn btn-primary">Torneos</a>
        <a href="{{ route('admin.user.index') }}" role="button" class="btn btn-primary">Usuarios</a>
    </div>
@endsection

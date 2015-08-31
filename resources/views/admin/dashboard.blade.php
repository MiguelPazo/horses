@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Dashboard
    </h3>

    <div class="panel-body">
        <a href="{{ route('admin.tournament.index') }}" role="button" class="btn btn-primary btn-lg btn_dashboard">Concursos</a>
        <a href="{{ route('admin.user.index') }}" role="button" class="btn btn-primary btn-lg btn_dashboard">Usuarios</a>
    </div>
@endsection

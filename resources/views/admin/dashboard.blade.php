@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center text-primary">
                Dashboard
            </h3>

            <div class="panel-body">
                <a href="{{ route('admin.tournament.index') }}" role="button" class="btn btn-primary">Torneos</a>
                <a href="" role="button" class="btn btn-primary">Usuarios</a>
                <a href="" role="button" class="btn btn-primary">Jurados</a>
                <a href="" role="button" class="btn btn-primary">Categorias</a>
            </div>
        </div>
    </div>
@endsection

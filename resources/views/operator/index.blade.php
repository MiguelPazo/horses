@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Catalogo para {{ $tournament }}
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <a href="{{ route('admin.user.create') }}" class="btn btn-primary">Nuevo</a>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>Conectado</th>
                    <th>Perfil</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection

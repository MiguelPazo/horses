@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Catalogo para {{ $tournament }}
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <a href="{{ route('oper.animal.create') }}" class="btn btn-primary">Nuevo</a>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Prefijo</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Fecha de Nac.</th>
                    <th>Categorías</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <tr>

                </tr>

                </tbody>
            </table>
        </div>
    </div>
@endsection

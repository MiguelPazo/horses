@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Auditoria de Usuarios
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Usuario</th>
                    <th>IP</th>
                    <th>Fecha</th>
                </tr>
                </thead>
                <tbody>
                @foreach( $lstAudit as  $audit)
                    <tr>
                        <td>{{ $audit->id }}</td>
                        <td>{{ $audit->user }}</td>
                        <td>{{ $audit->ip }}</td>
                        <td>{{ $audit->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

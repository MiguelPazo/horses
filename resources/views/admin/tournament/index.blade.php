@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Listado de Torneos
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <a href="{{ route('admin.tournament.create') }}" class="btn btn-primary">Nuevo</a>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Torneo</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Cierre</th>
                    <th>Cantidad de categorias</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1; ?>
                @foreach( $lstTournaments as  $tournament)
                    <tr class="{{ ($tournament->status == \Horses\Constants\ConstDb::STATUS_ACTIVE)? 'info' : '' }}">
                        <th scope="row">{{ $count }}</th>
                        <td>{{ $tournament->description }}</td>
                        <td>{{ $tournament->date_begin }}</td>
                        <td>{{ $tournament->date_end }}</td>
                        <td>5</td>
                        <td>
                            @if( $tournament->status == \Horses\Constants\ConstDb::STATUS_ACTIVE )
                                <a href="{{ route('admin.tournament.disable', $tournament->id) }}" role="button"
                                   class="btn btn-success">
                                    <span class="glyphicon glyphicon-star"></span>
                                </a>
                            @else
                                <a href="{{ route('admin.tournament.enable', $tournament->id) }}" role="button"
                                   class="btn">
                                    <span class="glyphicon glyphicon-star"></span>
                                </a>
                            @endif

                            <a href="{{ route('admin.tournament.edit', $tournament->id) }}" role="button"
                               class="btn">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>

                            <a href="{{ route('admin.tournament.category', $tournament->id) }}" role="button"
                               class="btn">
                                <span class="glyphicon glyphicon-th-list"></span>
                            </a>

                            {{--<a href="{{ route('admin.tournament.destroy', $tournament->id) }}" role="button"--}}
                            {{--class="btn" data-method="delete">--}}
                            {{--<span class=" glyphicon glyphicon-trash"></span>--}}
                            {{--</a>--}}
                        </td>
                    </tr>
                    <?php $count++; ?>
                @endforeach
                </tbody>
            </table>
            <a href="{{ route('admin.tournament.create') }}" class="btn btn-primary">Nuevo</a>
        </div>
    </div>
@endsection

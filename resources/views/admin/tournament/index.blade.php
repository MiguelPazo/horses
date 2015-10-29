@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Listado de Concursos
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <a href="{{ route('admin.tournament.create') }}" class="btn btn-primary">Nuevo</a>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Concurso</th>
                    <th>¿Juez de Turno?</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Cierre</th>
                    <th>Activo</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1; ?>
                @foreach( $lstTournaments as  $tournament)
                    <tr class="{{ ($tournament->status == \Horses\Constants\ConstDb::STATUS_ACTIVE)? 'info' : '' }}">
                        <th scope="row">{{ $count }}</th>
                        <td>{{ $tournament->description }}</td>
                        <td>
                            @if($tournament->type == \Horses\Constants\ConstDb::TYPE_TOURNAMENT_JURY)
                                Sí
                            @else
                                No
                            @endif
                        </td>
                        <td>{{ $tournament->date_begin }}</td>
                        <td>{{ $tournament->date_end }}</td>
                        <td>
                            @if( $tournament->status == \Horses\Constants\ConstDb::STATUS_ACTIVE )
                                <a href="{{ route('admin.tournament.disable', $tournament->id) }}" role="button"
                                   class="btn btn-success btn_link_prevent">
                                    <span class="glyphicon glyphicon-star"></span>
                                </a>
                            @else
                                <a href="{{ route('admin.tournament.enable', $tournament->id) }}" role="button"
                                   class="btn btn_link_prevent">
                                    <span class="glyphicon glyphicon-star"></span>
                                </a>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('tournament.results', $tournament->id) }}" role="button"
                               class="btn" target="_blank">
                                <span class="glyphicon glyphicon-blackboard"></span>
                            </a>

                            <a href="{{ route('admin.tournament.category', $tournament->id) }}" role="button"
                               class="btn">
                                <span class="glyphicon glyphicon-th-list"></span>
                            </a>

                            @if($tournament->status == \Horses\Constants\ConstDb::STATUS_INACTIVE)
                                <a href="{{ route('admin.tournament.edit', $tournament->id) }}" role="button"
                                   class="btn">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>

                                <a href="{{ route('admin.tournament.destroy', $tournament->id) }}" role="button"
                                   class="btn btn_link_prevent" data-method="delete">
                                    <span class=" glyphicon glyphicon-trash"></span>
                                </a>
                            @endif
                        </td>
                    </tr>
                    <?php $count++; ?>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

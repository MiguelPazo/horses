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
                    <th>¿Puntaje por promedio?</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Cierre</th>
                    <th>Activo</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1; ?>
                @foreach( $lstTournaments as  $tournament)
                    <tr class="{{ ($tournament->status == \Horses\Constants\ConstDb::STATUS_ACTIVE ||
                                $tournament->status == \Horses\Constants\ConstDb::STATUS_JOURNAL )? 'info' : '' }}">
                        <th scope="row">{{ $count }}</th>
                        <td>{{ $tournament->description }}</td>
                        <td class="text-center">
                            @if($tournament->type == \Horses\Constants\ConstDb::TYPE_TOURNAMENT_JURY)
                                Sí
                            @else
                                No
                            @endif
                        </td>
                        <td>{{ $tournament->date_begin }}</td>
                        <td>{{ $tournament->date_end }}</td>
                        <td>
                            @if( $tournament->status == \Horses\Constants\ConstDb::STATUS_ACTIVE ||
                                $tournament->status == \Horses\Constants\ConstDb::STATUS_JOURNAL)
                                <a href="{{ route('admin.tournament.disable', $tournament->id) }}" role="button"
                                   class="btn btn-success btn_link_prevent">
                                    <span class="glyphicon glyphicon-star"></span>
                                </a>
                            @elseif($tournament->status == \Horses\Constants\ConstDb::STATUS_INACTIVE)
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

                            @if( $tournament->status == \Horses\Constants\ConstDb::STATUS_ACTIVE)
                                <a role="button" class="btn btn_catalog" rel="{{ $tournament->id }}">
                                    <span class="glyphicon glyphicon-check"></span>
                                </a>
                            @else
                                <a href="{{ url('/catalog/report/' . $tournament->id) }}" class="btn" target="_blank">
                                    <span class="glyphicon glyphicon-check"></span>
                                </a>
                            @endif

                            @if( $tournament->status == \Horses\Constants\ConstDb::STATUS_ACTIVE)
                                <a href="{{ route('tournament.change.journal', ['idTournament' =>  $tournament->id, 'status' => 1]) }}"
                                   rel="Una vez inicie la jornada de un concurso no podrá detenerlo, ¿esta seguro?"
                                   role="button" class="btn btn_link_prevent">
                                    <span class="glyphicon glyphicon-play"></span>
                                </a>
                            @endif

                            @if( $tournament->status == \Horses\Constants\ConstDb::STATUS_JOURNAL)
                                <a href="{{ route('tournament.change.journal', ['idTournament' =>  $tournament->id, 'status' => 0]) }}"
                                   rel="Una vez finalice la jornada de un concurso no podrá iniciarlo, ¿esta seguro?"
                                   role="button" class="btn btn_link_prevent">
                                    <span class="glyphicon glyphicon-stop"></span>
                                </a>
                            @endif

                            @if($tournament->status == \Horses\Constants\ConstDb::STATUS_INACTIVE)
                                <a href="{{ route('admin.tournament.edit', $tournament->id) }}" role="button"
                                   class="btn">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>

                                <a href="{{ route('admin.tournament.destroy', $tournament->id) }}" role="button"
                                   rel="¿Esta seguro que desea eliminar el concurso {{ $tournament->description }}?"
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
    @include('admin.tournament._partials.popup')
@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/libs/jquery-ui/ui/minified/i18n/datepicker-es.js') }}"></script>
    <script src="{{ asset('/js/app/admin/tournament.js') }}"></script>
@endsection

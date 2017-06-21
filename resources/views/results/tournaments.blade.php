@extends('layout', ['classResult' => (!Auth::check()) ? 'black-ground' : ''])

@section('content')
    <h3 class="text-center text-primary">
        Listado de Concursos
    </h3>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Concurso</th>
                    <th>¿Puntaje por promedio?</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Cierre</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1; ?>
                @foreach( $lstTournaments as  $tournament)
                    <tr>
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
                            <a href="{{ route('tournament.results', $tournament->id) }}" role="button"
                               class="btn" target="_blank">
                                <span class="glyphicon glyphicon-blackboard"></span>
                            </a>
                        </td>
                    </tr>
                    <?php $count++; ?>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

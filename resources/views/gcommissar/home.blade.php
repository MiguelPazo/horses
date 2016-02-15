@extends('layout')

@section('content')
    <h5><b>Concurso: {{ $oTournament->description }}</b></h5>
    @if($wData)
        <h3 class="text-center text-primary">
            {{ $oCategory->description}}
        </h3>
        <div class="col-md-12">
            @if($selection)
                <div class="tab-pane">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Competidores Seleccionados ({{ $lstCompetitorWinners->count() }})</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lstCompetitorWinners as $competitor)
                                <tr>
                                    <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Competidores No Seleccionados ({{ $lstCompetitorHonorable->count() }})</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lstCompetitorHonorable as $competitor)
                                <tr>
                                    <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="tab-pane">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th rowspan="2">Puesto</th>
                                <th rowspan="2">Número de Cancha</th>
                                <th rowspan="2">Número de Catálogo</th>
                                <th colspan="{{ $oCategory->juries->count() + 1 }}" class="center">Primera Clasificación
                                </th>
                                <th colspan="{{ $oCategory->juries->count() + 1 }}" class="center">Segunda Clasificación
                                </th>
                            </tr>
                            <tr>
                                @foreach($oCategory->juries as $jury)
                                    <th class="center {{ ($jury->id == $juryDiriment->user_id)? 'active':'' }}">{{ $jury->names .' '. substr($jury->lastname,0,1) . '.' }}</th>
                                @endforeach
                                <th class="center success">Total</th>

                                @foreach($oCategory->juries as $jury)
                                    <th class="center {{ ($jury->id == $juryDiriment->user_id)? 'active':'' }}">{{ $jury->names .' '. substr($jury->lastname,0,1) . '.' }}</th>
                                @endforeach
                                <th class="center success">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $position = 1; ?>
                            @foreach($lstCompetitorWinners as $competitor)
                                <tr>
                                    <td scope="row">{{ $position }}</td>
                                    <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                    <td scope="row">{{ $competitor->catalog }}</td>
                                    <?php $acum = 0 ?>
                                    @foreach($competitor->stages as $stage)
                                        @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                                            <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                                            <?php $acum += $stage->position ?>
                                        @endif
                                    @endforeach
                                    <td class="center success">{{ $acum }}</td>

                                    @if($showSecond)
                                        <?php $acum = 0 ?>
                                        @foreach($competitor->stages as $stage)
                                            @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_2)
                                                <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                                                <?php $acum += $stage->position ?>
                                            @endif
                                        @endforeach
                                        <td class="center success">{{ $acum }}</td>
                                    @endif
                                </tr>
                                <?php $position++; ?>
                            @endforeach

                            <?php $position = 1; ?>
                            @foreach($lstCompetitorHonorable as $competitor)
                                <tr>
                                    <td scope="row">MH{{ $position }}</td>
                                    <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                    <td scope="row">{{ $competitor->catalog }}</td>

                                    <?php $acum = 0 ?>
                                    @foreach($competitor->stages as $stage)
                                        @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                                            <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                                            <?php $acum += $stage->position ?>
                                        @endif
                                    @endforeach
                                    <td class="center success">{{ $acum }}</td>
                                </tr>
                                <?php $position++; ?>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        @if($suggest != 0)
            <a class="suggest btn btn-success btn-lg" href="{{ url('/general-commissar', $suggest) }}">
                Ver Siguiente <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
        @endif
    @endif
@endsection


@section('scripts')
    <script src="{{ asset('/js/app/results.js') }}"></script>
@endsection

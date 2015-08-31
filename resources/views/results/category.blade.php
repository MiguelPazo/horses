@extends('layout')

@section('content')
    @include('results._partials.header')
    <div class="col-md-10">
        <div class="col-md-12">
            <div class="tab-pane" id="pane_stage">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th rowspan="2">Puesto</th>
                            <th rowspan="2">Número de Participante</th>
                            <th colspan="{{ $lstJury->count() + 1 }}" class="center">Primera Clasificación</th>
                            <th colspan="{{ $lstJury->count() + 1 }}" class="center">Segunda Clasificación</th>
                        </tr>
                        <tr>
                            @foreach($lstJury as $jury)
                                <th class="center {{ ($jury->id == $dirimentId)? 'active':'' }}">{{ $jury->names .' '. substr($jury->lastname,0,1) . '.' }}</th>
                            @endforeach
                            <th class="center success">Total</th>

                            @foreach($lstJury as $jury)
                                <th class="center {{ ($jury->id == $dirimentId)? 'active':'' }}">{{ $jury->names .' '. substr($jury->lastname,0,1) . '.' }}</th>
                            @endforeach
                            <th class="center success">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $position = 1; ?>
                        @foreach($lstCompetitorWinners as $competitor)
                            <tr>
                                <th scope="row">{{ $position }}</th>
                                <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>

                                <?php $acum = 0 ?>
                                @foreach($competitor->stages as $stage)
                                    @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                                        <td class="center {{ ($stage->jury->id == $dirimentId) ? 'active':'' }}">{{ $stage->position }}</td>
                                        <?php $acum += $stage->position ?>
                                    @endif
                                @endforeach
                                <td class="center success">{{ $acum }}</td>

                                @if($showSecond)
                                    <?php $acum = 0 ?>
                                    @foreach($competitor->stages as $stage)
                                        @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_2)
                                            <td class="center {{ ($stage->jury->id == $dirimentId) ? 'active':'' }}">{{ $stage->position }}</td>
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
                                <th scope="row">MH{{ $position }}</th>
                                <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>

                                <?php $acum = 0 ?>
                                @foreach($competitor->stages as $stage)
                                    @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                                        <td class="center {{ ($stage->jury->id == $dirimentId) ? 'active':'' }}">{{ $stage->position }}</td>
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
        </div>
    </div>

@endsection

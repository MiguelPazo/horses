@if($selection)
    <div class="tab-pane">
        <div class="table-responsive">
            <b>Competidores Seleccionados ({{ $lstCompetitorWinners->count() }})</b>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="20%">N° de Cancha</th>
                    <th width="20%">N° de Catálogo</th>
                    <th width="20%">Prefijo</th>
                    <th width="40%">Nombre</th>
                </tr>
                </thead>
                <tbody>
                @if($oCategory->mode == \Horses\Constants\ConstDb::MODE_PERSONAL)
                    @foreach($lstCompetitorWinners as $competitor)
                        <tr>
                            <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                            <td>{{ $competitor->catalog }}</td>
                            <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                            <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                        </tr>
                    @endforeach
                @else
                    @foreach($lstCompetitorWinners as $lstCompetitor)
                        <?php $print = true; ?>
                        @foreach($lstCompetitor as $competitor)
                            <tr>
                                @if($print)
                                    <td rowspan="{{ $lstCompetitor->count() }}">{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                @endif
                                <td>{{ $competitor->catalog }}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                            </tr>
                            <?php $print = false; ?>
                        @endforeach
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane">
        <div class="table-responsive">
            <b>Competidores No Seleccionados ({{ $lstCompetitorHonorable->count() }})</b>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="20%">N° de Cancha</th>
                    <th width="20%">N° de Catálogo</th>
                    <th width="20%">Prefijo</th>
                    <th width="40%">Nombre</th>
                </tr>
                </thead>
                <tbody>
                @if($oCategory->mode == \Horses\Constants\ConstDb::MODE_PERSONAL)
                    @foreach($lstCompetitorHonorable as $competitor)
                        <tr>
                            <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                            <td>{{ $competitor->catalog }}</td>
                            <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                            <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                        </tr>
                    @endforeach
                @else
                    @foreach($lstCompetitorHonorable as $lstCompetitor)
                        <?php $print = true; ?>
                        @foreach($lstCompetitor as $competitor)
                            <tr>
                                @if($print)
                                    <td rowspan="{{ $lstCompetitor->count() }}">{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                @endif
                                <td>{{ $competitor->catalog }}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                            </tr>
                            <?php $print = false; ?>
                        @endforeach
                    @endforeach
                @endif
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
                    <th rowspan="2">N° de Cancha</th>
                    <th rowspan="2">N° de Catálogo</th>
                    <th rowspan="2">Prefijo</th>
                    <th rowspan="2">Nombre</th>
                    <th colspan="{{ $oCategory->juries->count() + 1 }}" class="center">
                        Primera Clasificación
                    </th>
                    <th colspan="{{ $oCategory->juries->count() + 1 }}" class="center">
                        Segunda Clasificación
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
                @if($oCategory->mode == \Horses\Constants\ConstDb::MODE_PERSONAL)
                    @include('results._partials.personal')
                @else
                    @include('results._partials.group')
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endif
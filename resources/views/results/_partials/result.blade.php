@if($selection)
    <div class="tab-pane">
        <div class="table-responsive">
            <b>Competidores {{ ($assistance)? 'Presentes':'Seleccionados' }} ({{ $lstCompetitorWinners->count() }})</b>
            <table class="table table-striped">
                <thead class="{{ ($complete)? 'content-bold-red':''}}">
                <tr>
                    @if($limp)
                        <th rowspan="2" width="5%">Claudicar</th>
                    @endif
                    <th width="15%">N° de Cancha</th>
                    @if($complete)
                        <th width="15%">N° de Catálogo</th>
                        <th width="10%">Prefijo</th>
                        <th width="15%">Nombre</th>
                        <th width="40%">Propietario</th>
                    @endif
                </tr>
                </thead>
                <tbody class="{{ ($complete)? 'content-bold':''}}">
                @if($oCategory->mode == \Horses\Constants\ConstDb::MODE_PERSONAL)
                    @foreach($lstCompetitorWinners as $competitor)
                        <tr>
                            @if($limp)
                                <td scope="row" width="1%">
                                    <a href="{{ url('/general-commissar/category/' . $competitor->category_id . '/limp/' . $competitor->id) }}"
                                       class="btn_link_prevent"
                                       rel="¿Esta seguro que desea claudicar al competiro con número {{ $competitor->number }}?">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                </td>
                            @endif
                            <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                            @if($complete)
                                <td>{{ $competitor->catalog }}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->owner :''}}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    @foreach($lstCompetitorWinners as $lstCompetitor)
                        <?php $print = true; ?>
                        @foreach($lstCompetitor as $competitor)
                            <tr>
                                @if($print)
                                    @if($limp)
                                        <td scope="row" width="1%" rowspan="{{ $lstCompetitor->count() }}">
                                            <a href="{{ url('/general-commissar/category/' . $competitor->category_id . '/limp/' . $competitor->id) }}"
                                               class="btn_link_prevent"
                                               rel="¿Esta seguro que desea claudicar al competiro con número {{ $competitor->number }}?">
                                                <span class="glyphicon glyphicon-remove"></span>
                                            </a>
                                        </td>
                                    @endif
                                    <td rowspan="{{ $lstCompetitor->count() }}">{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                @endif

                                @if($complete)
                                    <td>{{ $competitor->catalog }}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->owner :''}}</td>
                                @endif
                            </tr>
                            <?php $print = false; ?>
                        @endforeach
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    @if(!$assistance)
        <div class="tab-pane">
            <div class="table-responsive">
                <b>Competidores No Seleccionados ({{ $lstCompetitorHonorable->count() }})</b>
                <table class="table table-striped">
                    <thead class="{{ ($complete)? 'content-bold-red':''}}">
                    <tr>
                        <th width="15%">N° de Cancha</th>
                        @if($complete)
                            <th width="15%">N° de Catálogo</th>
                            <th width="10%">Prefijo</th>
                            <th width="15%">Nombre</th>
                            <th width="40%">Propietario</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="{{ ($complete)? 'content-bold':''}}">
                    @if($oCategory->mode == \Horses\Constants\ConstDb::MODE_PERSONAL)
                        @foreach($lstCompetitorHonorable as $competitor)
                            <tr>
                                <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                @if($complete)
                                    <td>{{ $competitor->catalog }}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->owner :''}}</td>
                                @endif
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
                                    @if($complete)
                                        <td>{{ $competitor->catalog }}</td>
                                        <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                                        <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                                        <td>{{ isset($competitor->animal_details)? $competitor->animal_details->owner :''}}</td>
                                    @endif
                                </tr>
                                <?php $print = false; ?>
                            @endforeach
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@else
    <div class="tab-pane">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="{{ ($complete)? 'content-bold-red':''}}">
                <tr>
                    @if($limp)
                        <th rowspan="2">Claudicar</th>
                    @endif
                    <th rowspan="2">Puesto</th>
                    <th rowspan="2">N° de Cancha</th>
                    @if($complete)
                        <th rowspan="2">N° de Catálogo</th>
                        <th rowspan="2">Prefijo</th>
                        <th rowspan="2">Nombre</th>
                        <th rowspan="2">Propietario</th>
                    @endif
                    <th colspan="{{ $oCategory->juries->count() + 1 }}" class="center">
                        Clasificación
                    </th>
                    <th colspan="{{ $oCategory->juries->count() + 1 }}" class="center">
                        Comprobación
                    </th>
                </tr>
                <tr>
                    @foreach($oCategory->juries as $jury)
                        <th class="center {{ ($jury->id == $juryDiriment->user_id)? 'active active-diriment':'' }}">{{ $jury->names .' '. substr($jury->lastname,0,1) . '.' }}</th>
                    @endforeach
                    <th class="center result-final">Total</th>

                    @foreach($oCategory->juries as $jury)
                        <th class="center {{ ($jury->id == $juryDiriment->user_id)? 'active active-diriment':'' }}">{{ $jury->names .' '. substr($jury->lastname,0,1) . '.' }}</th>
                    @endforeach
                    <th class="center result-final">Total</th>
                </tr>
                </thead>
                <tbody  class="{{ ($complete)? 'content-bold':''}}">
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

@if($lstCompetitorLimp->count() > 0)
    <div class="tab-pane">
        <div class="table-responsive">
            <b>Competidores Claudicados ({{ $lstCompetitorLimp->count() }})</b>
            <table class="table table-striped">
                <thead class="{{ ($complete)? 'content-bold-red':''}}">
                <tr>
                    <th width="10%">N° de Cancha</th>
                    @if($complete)
                        <th width="15%">N° de Catálogo</th>
                        <th width="10%">Prefijo</th>
                        <th width="20%">Nombre</th>
                        <th width="45%">Propietario</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @if($oCategory->mode == \Horses\Constants\ConstDb::MODE_PERSONAL)
                    @foreach($lstCompetitorLimp as $competitor)
                        <tr>
                            <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                            @if($complete)
                                <td>{{ $competitor->catalog }}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                                <td>{{ isset($competitor->animal_details)? $competitor->animal_details->owner :''}}</td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    @foreach($lstCompetitorLimp as $lstCompetitor)
                        <?php $print = true; ?>
                        @foreach($lstCompetitor as $competitor)
                            <tr>
                                @if($print)
                                    <td rowspan="{{ $lstCompetitor->count() }}">{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
                                @endif
                                @if($complete)
                                    <td>{{ $competitor->catalog }}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->prefix :''}}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->name :''}}</td>
                                    <td>{{ isset($competitor->animal_details)? $competitor->animal_details->owner :''}}</td>
                                @endif
                            </tr>
                            <?php $print = false; ?>
                        @endforeach
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endif
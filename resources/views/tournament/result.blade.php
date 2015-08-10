@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        {{ $oTournament->description }}
    </h3>

    <p><b>Categoria: </b> {{ $oCategory->description }}</p>

    <div class="tab-pane" id="pane_stage">
        <div class="row row_sorteable">
            <div class="col-md-6">
                <h3> Ganadores </h3>

                <div class="comp_list">
                    <ul class="ul_comp_list">
                        <?php $position = 1; ?>
                        @foreach($lstCompetitorLeft as $competitor)
                            <li>
                                <div class="btn btn-block btn-lg btn-primary">
                                    ({{ $position++ }}°) Participante
                                    #{{ str_pad($competitor->number, 2, "0", STR_PAD_LEFT) }}
                                </div>
                            </li>

                        @endforeach
                    </ul>
                </div>

            </div>
            <div class="col-md-6">
                <h3> Premio Honroso </h3>

                <div class="comp_list">
                    <ul class="ul_comp_list">
                        @foreach($lstCompetitorRight as $competitor)
                            <li>
                                <div class="btn btn-block btn-lg btn-primary">
                                    ({{ $position++ }}°) Participante
                                    #{{ str_pad($competitor->number, 2, "0", STR_PAD_LEFT) }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

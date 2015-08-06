@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @include('tournament.partials.header')

            <div class="tabbable">
                <p><b>Categoria: </b> {{ Session::get('category')->nombre }}</p>

                <p><b>Etapa: </b> {{ $stage }}</p>

                <div class="tab-pane" id="pane_stage">
                    <p></p>

                    @if($valid)
                        <div class="row">
                            <div class="col-md-12">
                                <p></p>

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
                                                            #{{ str_pad($competitor->numero, 2, "0", STR_PAD_LEFT) }}
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
                                                            #{{ str_pad($competitor->numero, 2, "0", STR_PAD_LEFT) }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                @else
                    <h4>{{ $message }}</h4>
                @endif
                <p></p>
            </div>
        </div>
        <input id="process" type="hidden" name="process" value="1"/>
    </div>
@endsection

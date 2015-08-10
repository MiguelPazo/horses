@extends('layout')

@section('content')
    @include('tournament.partials.header')
    <div class="panel-body">

        {!! Form::open(array('url' => $post, 'id'=>'form_pane', 'method' => 'GET')) !!}
        <div class="tab-pane" id="pane_stage">
            <p></p>

            @if($valid)
                <div class="row row_sorteable">
                    <div class="col-md-6">
                        <h3> Clasifique a los Participantes </h3>

                        <div class="comp_list">
                            <ul class="ul_comp_list">
                                @foreach($lstCompetitor as $competitor)
                                    <li>
                                        <div class="btn btn-block btn-lg btn-primary">
                                            Participante
                                            #{{ str_pad($competitor->number, 2, "0", STR_PAD_LEFT) }}
                                            <input type="hidden" name="comp_{{ $competitor->id }}"
                                                   value="0"/>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <h3> Participantes Clasificados </h3>

                        <div class="comp_classify">
                            <ul class="ul_comp_list">
                                <li></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <h4>{{ $message }}</h4>
            @endif
            <p></p>
            <input id="process" type="hidden" name="process" value="1"/>
            {!! Form::close() !!}
        </div>
    </div>

    @include('tournament.partials.popup')

    <script src="{{ asset('/js/classify.js') }}"></script>
@endsection

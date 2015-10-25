@extends('layout')

@section('content')
    @include('tournament._partials.header')

    <div class="panel-body">
        <h3>Concursantes de la Categoría: {{ $count }} </h3>
        {!! Form::open(array('url' => route('tournament.save.selection'), 'id'=>'form_pane', 'method' => 'GET'))!!}
        <div class="tab-pane" id="pane_stage">
            <p></p>
            @foreach($lstCompetitor as $competitor)
                <button type="button" class="btn_competitor btn btn-block btn-lg btn-primary">
                    Participante #{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}
                </button>
                <input type="hidden" name="comp_{{ $competitor->id }}" value="0"/>
            @endforeach
            <p></p>
        </div>

        <input id="process" type="hidden" name="process" value="1"/>
        {!! Form::close() !!}

        <div class="count_selected" id="count_sel">
            0
        </div>
    </div>

    <script src="{{ asset('/js/selection.js') }}"></script>
@endsection

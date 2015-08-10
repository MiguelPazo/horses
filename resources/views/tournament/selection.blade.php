@extends('layout')

@section('content')
    @include('tournament.partials.header')

    <div class="panel-body">
        {!! Form::open(array('url' => route('tournament.save.selection'), 'id'=>'form_pane', 'method' => 'GET'))!!}
        <div class="tab-pane" id="pane_stage">
            <p></p>
            @foreach($lstCompetitor as $competitor)
                <button type="button" class="btn_competitor btn btn-block btn-lg btn-primary">
                    Participante #{{ str_pad($competitor->number, 2, "0", STR_PAD_LEFT) }}
                </button>
                <input type="hidden" name="comp_{{ $competitor->id }}" value="0"/>
            @endforeach
            <p></p>
        </div>

        <input id="process" type="hidden" name="process" value="1"/>
        {!! Form::close() !!}
    </div>

    @include('tournament.partials.popup')

    <script src="{{ asset('/js/selection.js') }}"></script>
@endsection

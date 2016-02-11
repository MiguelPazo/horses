@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        {{ $oTournament->description }}
    </h3>

    <p><b>Categoria: </b> {{ $oCategory->description }}</p>

    <div class="panel-body">
        <h3>Concursantes de la Categoría: {{ $oCategory->count_competitors }} </h3>

        {!! Form::open(['url' => route('commissar.assistance.save', $oCategory->id), 'id' => 'form']) !!}
        <div class="tabbable">
            <div class="tab-pane" id="pane_stage">
                <p></p>

                <div class="row">
                    <div class="col-md-12">
                        <p></p>
                        <?php $i = 0?>
                        @for($i; $i < $oCategory->count_competitors; $i++)
                            <button type="button" class="btn_competitor btn btn-block btn-lg btn-primary">
                                Participante #{{ str_pad($i + $oCategory->num_begin, $rpad, "0", STR_PAD_LEFT) }}
                            </button>
                            <input type="hidden" name="comp_{{ $i + $oCategory->num_begin }}" value="0"/>
                        @endfor
                        <input type="hidden" id="last_pos" value="{{ $i + $oCategory->num_begin }}">
                        <button type="button" class="btn_add btn btn-block btn-lg btn-info">
                            Agregar Adicional
                        </button>
                        <input type="hidden" name="" value="0"/>
                    </div>
                </div>
                <p></p>
            </div>
        </div>

        <a id="btn_confirm" role="button" class="btn btn-danger">Confirmar</a>

        {!! Form::close() !!}

        <div class="count_selected" id="count_sel">
            0
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/app/commissar/assistance.js') }}"></script>
@endsection

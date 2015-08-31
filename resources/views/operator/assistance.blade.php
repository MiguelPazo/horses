@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        {{ Session::get('oTournament')->description }}
    </h3>

    <p><b>Categoria: </b> {{ Session::get('oCategory')->description }}</p>

    <div class="panel-body">
        <h3>Concursantes de la Categoría: {{ $oCategory->count_competitors }} </h3>

        {!! Form::open(array('url' => route('operator.assistance.save'), 'id'=>'form')) !!}
        <div class="tabbable">
            <div class="tab-pane" id="pane_stage">
                <p></p>

                <div class="row">
                    <div class="col-md-12">
                        <p></p>
                        @for($i = 0; $i < $oCategory->count_competitors; $i++)
                            <button type="button" class="btn_competitor btn btn-block btn-lg btn-primary">
                                Participante #{{ str_pad($i + $oCategory->num_begin, $rpad, "0", STR_PAD_LEFT) }}
                            </button>
                            <input type="hidden" name="comp_{{ $i + $oCategory->num_begin }}" value="0"/>
                        @endfor
                    </div>
                </div>
                <p></p>
            </div>
        </div>

        <a id="modal" href="#modal-container" role="button" class="btn btn-danger"
           data-toggle="modal">Confirmar</a>

        {!! Form::close() !!}

        <div class="count_selected" id="count_sel">
            0
        </div>
    </div>

    <div class="modal fade" id="modal-container" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true"> ×
                    </button>
                    <h4 class="modal-title" id="myModalLabel"> Advertencia </h4>
                </div>
                <div class="modal-body">
                    Sólo se puede tomar asistencia una única ves, ¿esta seguro que desea confirmar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btn_confirm"> Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('/js/assistance.js') }}"></script>
@endsection

@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('/js/libs/jquery-ui/themes/base/jquery-ui.min.css') }}">
@endsection

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
                        <button type="button" id="btn_add" class="btn btn-block btn-lg btn-info">
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

    <div class="modal fade" id="modal_new_animal" role="dialog"
         aria-labelledby="modal_max_select_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true"> ×
                    </button>
                    <h4 class="modal-title"> Nuevo Competidor </h4>
                </div>
                {!! Form::open(['route' => ['oper.animal.store', $oCategory->id],'method' => 'POST', 'id' => 'formAnimal', 'class' => 'formuppertext']) !!}
                <div class="modal-body">
                    <p class="bg-danger" id="error_message" style="display: none">Error</p>

                    @include('commissar._partials.fields')

                </div>
                <div class="modal-footer">
                    {!! Form::submit('Guardar', ['class' => 'btn btn-default btn_disable']) !!}
                    <button type="button" class="btn btn-default btn_disable" data-dismiss="modal">
                        Cancelar
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/libs/jquery-ui/ui/minified/i18n/datepicker-es.min.js') }}"></script>
    <script src="{{ asset('/js/libs/devbridge-autocomplete/dist/jquery.autocomplete.min.js') }}"></script>
    <script src="{{ asset('/js/app/oper/animal/maintenance_common.js') }}"></script>
    <script src="{{ asset('/js/app/commissar/assistance.js') }}"></script>
@endsection

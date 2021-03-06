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
        <h3>Concursantes de la Categoría: <span id="total_comp">{{ $oCategory->count_competitors }}</span></h3>

        {!! Form::open(['url' => route('commissar.assistance.save', $oCategory->id), 'id' => 'form']) !!}
        <div class="tabbable">
            <div class="tab-pane" id="pane_stage">
                <p></p>

                <div class="row">
                    <div id="content_competitors" class="col-md-12">
                        <p></p>
                        <?php $i = 0?>
                        @for($i; $i < count($catalog); $i++)
                            <div class="cont_btns">
                                @if($oCategory->mode == \Horses\Constants\ConstDb::MODE_GROUP)
                                    <a type="button" class="btn btn-default btn_plus_ingroup"
                                       rel="{{ $catalog[$i]['group'] }}">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                @endif
                                <div class="btn_competitor btn btn-block btn-lg btn-primary {{ ($catalog[$i]['present'])? 'btn-success':'' }}">
                                    <div class="path_left">
                                        N° Cancha: {{ str_pad($i + $oCategory->num_begin, $rpad, "0", STR_PAD_LEFT) }}
                                    </div>
                                    <div class="path_right">
                                        N° Catálogo: {{ $catalog[$i]['number'] }}
                                    </div>
                                </div>
                                <input type="hidden" name="comp_{{ $catalog[$i]['number']}}"
                                       value="{{ ($catalog[$i]['present'])? 1:0 }}"/>
                            </div>
                        @endfor
                    </div>
                    <input type="hidden" id="max_catalog" value="{{ $maxCatalog }}"/>
                    <input type="hidden" id="ids_selected" value="{{ $ids }}" name="ids_selected"/>
                    <input type="hidden" id="last_pos" value="{{ $i + $oCategory->num_begin - 1 }}">
                    <input type="hidden" id="total_present" value="{{ $totalPresent }}"/>
                    <input type="hidden" id="tournament" value="{{ $oTournament->id }}"/>

                    <div class="col-md-12">
                        <button type="button" id="btn_add" class="btn btn-block btn-lg btn-info">
                            Agregar Adicional
                        </button>
                    </div>
                </div>
                <p></p>
            </div>
        </div>

        <a id="btn_confirm" role="button" class="btn btn-success">Confirmar</a>
        <a href="{{ url('/commissar') }}" class="btn btn-danger">Cancelar</a>

        {!! Form::close() !!}

        <div class="count_selected" id="count_sel">
            {{ $totalPresent }}
        </div>
    </div>

    @include('commissar._partials.new_competitor')
@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/libs/jquery-ui/ui/minified/i18n/datepicker-es.js') }}"></script>
    <script src="{{ asset('/js/libs/devbridge-autocomplete/dist/jquery.autocomplete.min.js') }}"></script>
    <script>
        var group = {{ ($oCategory->mode == \Horses\Constants\ConstDb::MODE_PERSONAL) ? 'false':'true'  }};
    </script>
    <script src="{{ asset('/js/app/oper/animal/maintenance_common.js') }}"></script>
    <script src="{{ asset('/js/app/commissar/assistance.js') }}"></script>
@endsection

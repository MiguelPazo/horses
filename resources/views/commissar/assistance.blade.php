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
                    <div id="content_competitors" class="col-md-12">
                        <p></p>
                        <?php $i = 0?>
                        @for($i; $i < count($catalog); $i++)
                            <div class="btn_competitor btn btn-block btn-lg btn-primary
                            {{ ($catalog[$i]['present'])? 'btn-success':'' }}">
                                <div class="path_left">
                                    N° Cancha: {{ str_pad($i + $oCategory->num_begin, $rpad, "0", STR_PAD_LEFT) }}
                                </div>
                                <div class="path_right">
                                    N° Catálogo: {{ $catalog[$i]['number'] }}
                                </div>
                            </div>
                            <input type="hidden" name="comp_{{ $catalog[$i]['number']}}"
                                   value="{{ ($catalog[$i]['present'])? 1:0 }}"/>
                        @endfor

                        <input type="hidden" id="lst_catalog" value="{{ $catalogNumbers }}"/>
                        <input type="hidden" id="last_pos" value="{{ $i + $oCategory->num_begin }}">
                        <input type="hidden" id="total_present" value="{{ $totalPresent }}"/>
                        <input type="hidden" id="tournament" value="{{ $oTournament->id }}"/>
                    </div>
                    <button type="button" id="btn_add" class="btn btn-block btn-lg btn-info">
                        Agregar Adicional
                    </button>
                </div>
                <p></p>
            </div>
        </div>

        <a id="btn_confirm" role="button" class="btn btn-success">Confirmar</a>
        <a href="{{ url('/commissar') }}" class="btn btn-danger">Cancelar</a>

        {!! Form::close() !!}

        <div class="count_selected" id="count_sel">
            0
        </div>
    </div>

    @include('commissar._partials.new_competitor')
@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/libs/jquery-ui/ui/minified/i18n/datepicker-es.min.js') }}"></script>
    <script src="{{ asset('/js/libs/devbridge-autocomplete/dist/jquery.autocomplete.min.js') }}"></script>
    <script src="{{ asset('/js/app/oper/animal/maintenance_common.js') }}"></script>
    <script src="{{ asset('/js/app/commissar/assistance.js') }}"></script>
@endsection

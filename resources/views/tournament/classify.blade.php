@extends('layout')

@section('content')
    @include('tournament._partials.header')
    <div class="panel-body">

        {!! Form::open(array('url' => $post, 'id'=>'form_pane', 'method' => 'POST')) !!}
        <div class="tab-pane" id="pane_stage">
            <p></p>

            @if($valid)
                <div class="row row_sorteable">
                    <div class="col-md-6">
                        <h3> Clasifique a los Participantes (<span id="count_fclassify" class="count_class">0</span>)
                        </h3>

                        <div class="comp_list">
                            <ul class="ul_comp_list">
                                @foreach($lstCompetitor as $competitor)
                                    <li>
                                        <div class="btn btn-block btn-lg btn-primary">
                                            Participante
                                            #{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}
                                            <input type="hidden" name="comp_{{ $competitor->id }}"
                                                   value="0"/>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <h3> Participantes Clasificados (<span id="count_classify" class="count_class">0</span>)</h3>

                        <div class="comp_classify">
                            <ul class="ul_comp_list">
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

    <div class="modal fade" id="modal_verify" role="dialog"
         aria-labelledby="modal-container-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true"> ×
                    </button>
                    <h4 class="modal-title"> Confirmación de Clasificados </h4>
                </div>
                <div class="modal-body row_sorteable" id="space_verify">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" disabled="disabled" id="btn_confirm">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('/js/libs/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('/js/app/classify.js') }}"></script>
@endsection

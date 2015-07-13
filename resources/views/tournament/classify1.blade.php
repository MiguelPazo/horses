@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="title">
                <span> Bienvenido: {{ Auth::user()->nombres }}</span>
                <a href="{{ url('auth/logout') }}" role="button" class="btn btn-danger">SALIR</a>
            </div>

            {!! Form::open(array('url' => route('tournament.save.selection'), 'id'=>'form_pane', 'method' => 'GET')) !!}
            <div class="tabbable">
                <p><b>Categoria: </b> {{ Session::get('category')->nombre }}</p>

                <p><b>Etapa: </b> {{ $stage }}</p>

                <div class="tab-pane" id="pane_stage">
                    <p></p>

                    @if($valid)
                        <div class="row">
                            @include('tournament.partials.topButtons')
                            <p></p>

                            <div class="row">
                                <div class="col-md-6">
                                    <h3> Califique a los Participantes </h3>

                                    @foreach($lstCompetitor as $competitor)
                                        <button type="button" class="btn_competitor btn btn-block btn-lg btn-primary">
                                            Participante #{{ str_pad($competitor->numero, 2, "0", STR_PAD_LEFT) }}
                                        </button>
                                        <input type="hidden" name="comp_{{ $competitor->id }}"
                                               value="0"/>
                                        <p></p>
                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    <h3> Participantes Calificados </h3>
                                    <button type="button" class="btn btn-block btn-lg btn-success"> (1 lugar)
                                        Participante #13
                                    </button>
                                    <button type="button" class="btn btn-block btn-success btn-lg"> (2 lugar)
                                        Participante #14
                                    </button>
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
        {!! Form::close() !!}
    </div>
    </div>

    @include('tournament.partials.popup')

    <script src="{{ asset('js/classify_1.js') }}"></script>
@endsection

@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center text-primary">
                Sistema de Calificación
            </h3>
            <a id="modal-550758" href="#modal-container-550758" role="button" class="btn btn-primary"
               data-toggle="modal">Configuración</a>

            <p></p>

            {!! Form::open(array('url' => 'auth/login', 'id'=>'formLogin')) !!}
            <div class="modal fade" id="modal-container-550758" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">

                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                ×
                            </button>
                            <h4 class="modal-title" id="myModalLabel">
                                Configuración
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="campeonato">
                                    Campeonato Activo:
                                </label>
                                <input type="text" class="form-control" id="campeonato" readonly="yes"
                                       value="{{ $tournament->nombre }}"/>

                                <p></p>
                                <label for="categoria">
                                    Categoría:
                                </label>

                                <select class="form-control" name="category">
                                    @foreach($lstCategory as $category)
                                        <option value="{{ $category->id }}">{{ $category->nombre }}</option>
                                    @endforeach
                                </select>

                                <p></p>
                                <label>Juez Dirimente:</label>
                                <label class="radio-inline">
                                    <input type="radio" name="rad_jury_type" value="0" checked="checked"> No
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="rad_jury_type" value="1"> Sí
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="user">
                    Usuario
                </label>
                <input type="text" class="form-control" name="user" id="user"/>
            </div>
            <div class="form-group">
                <label for="password">
                    Password
                </label>
                <input type="password" class="form-control" name="password" id="password"/>
            </div>
            <button type="submit" class="btn btn-default">
                Ingresar
            </button>
            {!! Form::close() !!}
        </div>
    </div>

    <script src="{{ asset('/js/login.js') }}"></script>
@endsection

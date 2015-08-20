@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Sistema de Calificación
    </h3>

    {!! Form::open(array('url' => 'auth/login', 'id'=>'formLogin')) !!}
    <div class="form-group">
        <label for="user">
            Usuario:
        </label>
        <input type="text" class="form-control" name="user" id="user"/>
    </div>
    <div class="form-group">
        <label for="password">
            Contraseña:
        </label>
        <input type="password" class="form-control" name="password" id="password"/>
    </div>
    <button type="submit" class="btn btn-default">
        Ingresar
    </button>
    {!! Form::close() !!}

    <div class="modal fade" id="modal-container" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true"> ×
                    </button>
                    <h4 class="modal-title" id="modal_title"> Error </h4>
                </div>
                <div class="modal-body" id="modal_message">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn_end" data-dismiss="modal"> Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('/js/login.js') }}"></script>
@endsection

<div class="form-group">
    {!! Form::label('names', 'Nombres:') !!}
    {!! Form::text('names', (isset($oUser)) ? $oUser->names : null, ['class' => 'form-control required', 'maxlength' =>
    50]) !!}
</div>
<div class="form-group">
    {!! Form::label('lastname', 'Apellidos:') !!}
    {!! Form::text('lastname', (isset($oUser)) ? $oUser->lastname : null, ['class' => 'form-control required',
    'maxlength' =>
    80]) !!}
</div>
<div class="form-group">
    {!! Form::label('user', 'Usuario:') !!}
    {!! Form::text('user', (isset($oUser)) ? $oUser->user : null, ['class' => 'form-control required', 'maxlength' =>
    15]) !!}
</div>
<div class="form-group">
    {!! Form::label('password', 'Contraseña:') !!}
    @if($passRequired)
        {!! Form::password('password', ['class' => 'form-control required', 'maxlength' => 200])!!}
    @else
        {!! Form::password('password', ['class' => 'form-control', 'maxlength' => 200])!!}
    @endif
</div>
<div class="form-group">
    {!! Form::label('profile', 'Perfil:') !!}
    {!! Form::select('profile', ['commissar' => 'Comisario', 'jury' => 'Jurado'],
    (isset($oUser)) ? $oUser->profile : null,['class' =>'form-control required']) !!}
</div>
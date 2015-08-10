<div class="form-group">
    {!! Form::label('names', 'Nombres:') !!}
    {!! Form::text('names', (isset($oUser)) ? $oUser->names : null, ['class' => 'form-control', 'maxlenght' => 50]) !!}
</div>
<div class="form-group">
    {!! Form::label('lastname', 'Apellidos:') !!}
    {!! Form::text('lastname', (isset($oUser)) ? $oUser->lastname : null, ['class' => 'form-control', 'maxlenght' =>
    80]) !!}
</div>
<div class="form-group">
    {!! Form::label('user', 'Usuario:') !!}
    {!! Form::text('user', (isset($oUser)) ? $oUser->user : null, ['class' => 'form-control', 'maxlenght' => 15]) !!}
</div>
<div class="form-group">
    {!! Form::label('password', 'Contraseña:') !!}
    {!! Form::password('password', ['class' => 'form-control', 'maxlenght' =>
    200])!!}
</div>
<div class="form-group">
    {!! Form::label('profile', 'Perfil:') !!}
    {!! Form::select('profile', ['admin' => 'Administrador', 'operator' => 'Operador', 'jury' => 'Jurado'], null
    ,['class' =>'form-control']) !!}
</div>
{!! Form::open(['route' => ['oper.animal.store'],'method' => 'POST', 'id' => 'formAnimal',
'class' => 'formuppertext']) !!}
<p class="bg-danger contextual_message" id="error_message" style="display: none">Error</p>

<div class="form-group">
    {!! Form::label('name', 'Nombre:') !!}
    {!! Form::text('name', null, ['class' => 'form-control required no_disable', 'id'=> 'name', 'rel' => 'disable',
    'maxlength' => 45]) !!}
</div>
<div class="form-group">
    {!! Form::label('birthdate', 'Fecha de Nacimiento:') !!}
    {!! Form::text('birthdate', null, ['class' => 'datepicker form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('code', 'Código:') !!}
    {!! Form::text('code', null, ['class' => 'form-control', 'maxlength' => 20]) !!}
</div>
<div class="form-group">
    {!! Form::label('owner_name', 'Propietario:') !!}
    {!! Form::text('owner_name', null, ['class' => 'form-control namewlast complete_agents', 'maxlength' => 130])
    !!}
</div>
<div class="form-group">
    {!! Form::label('breeder_name', 'Criador:') !!}
    {!! Form::text('breeder_name', null, ['class' =>'form-control namewlast complete_agents', 'maxlength' => 50])
    !!}
</div>
<div class="form-group">
    {!! Form::label('prefix', 'Prefijo:') !!}
    {!! Form::text('prefix', null, ['class' => 'form-control', 'maxlength' => 50]) !!}
</div>
<div class="form-group">
    {!! Form::label('dad_name', 'Padre:') !!}
    {!! Form::text('dad_name', null, ['class' => 'form-control parents', 'maxlength' => 45]) !!}
</div>
<div class="form-group">
    {!! Form::label('mom_name', 'Madre:') !!}
    {!! Form::text('mom_name', null, ['class' => 'form-control parents', 'maxlength' => 45]) !!}
</div>
{!! Form::close() !!}
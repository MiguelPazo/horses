<div class="form-group">
    {!! Form::label('name', 'Nombre:') !!}
    {!! Form::text('name', (isset($oAnimal)) ? $oAnimal->name : null, ['class' => 'form-control required', 'id'=> 'name',
    'maxlength' => 45]) !!}
</div>
<div class="form-group">
    {!! Form::label('birthdate', 'Fecha de Nacimiento:') !!}
    {!! Form::text('birthdate', (isset($oAnimal)) ? $oAnimal->birthdate : null, ['class' => 'datepicker
    form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('code', 'Código:') !!}
    {!! Form::text('code', (isset($oAnimal)) ? $oAnimal->code : null, ['class' => 'form-control', 'maxlength' =>
    20]) !!}
</div>
<div class="form-group">
    {!! Form::label('categories_name', 'Categorías:', ['class' => 'block_label']) !!}
    {!! Form::select('categories_name', $lstCategory, (isset($lstCategorySelected)) ? $lstCategorySelected : null
    ,['class' =>'form-control', 'multiple' => 'multiple']) !!}
    {!! Form::hidden('categories', (isset($lstCategorySelected)) ? implode(',',$lstCategorySelected) : null, ['id' =>
    'categories']) !!}
</div>
<div class="form-group">
    {!! Form::label('owner_name', 'Propietario:') !!}
    {!! Form::text('owner_name', (isset($oOwner) && $oOwner != null) ? $oOwner->names . ', ' . $oOwner->lastnames : null, ['class' =>
    'form-control namewlast complete_agents', 'maxlength' => 130]) !!}
</div>
<div class="form-group">
    {!! Form::label('breeder_name', 'Criador:') !!}
    {!! Form::text('breeder_name', (isset($oBreeder) && $oBreeder != null) ? $oBreeder->names : null, ['class'
    =>'form-control namewlast complete_agents', 'maxlength' => 50]) !!}
</div>
<div class="form-group">
    {!! Form::label('prefix', 'Prefijo:') !!}
    {!! Form::text('prefix', (isset($oBreeder) && $oBreeder != null) ? $oBreeder->prefix : null, ['class' => 'form-control', 'maxlength' =>
    50]) !!}
</div>
<div class="form-group">
    {!! Form::label('dad_name', 'Padre:') !!}
    {!! Form::text('dad_name', (isset($oDad) && $oDad != null) ? $oDad->name : null, ['class' => 'form-control parents', 'maxlength' => 45]) !!}
</div>
<div class="form-group">
    {!! Form::label('mom_name', 'Madre:') !!}
    {!! Form::text('mom_name', (isset($oMom) && $oMom != null) ? $oMom->name : null, ['class' => 'form-control parents', 'maxlength' => 45]) !!}
</div>
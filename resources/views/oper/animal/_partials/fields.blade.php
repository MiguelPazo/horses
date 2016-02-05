<div class="form-group">
    {!! Form::label('name', 'Nombre:') !!}
    {!! Form::text('name', (isset($oAnimal)) ? $oAnimal->name : null, ['class' => 'form-control required', 'maxlength' =>
    50]) !!}
</div>
<div class="form-group">
    {!! Form::label('birthdate', 'Fecha de Nacimiento:') !!}
    {!! Form::text('birthdate', (isset($oAnimal)) ? $oAnimal->birthdate : null, ['class' => 'datepicker
    form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('code', 'Código:') !!}
    {!! Form::text('code', (isset($oAnimal)) ? $oAnimal->code : null, ['class' => 'form-control', 'maxlength' =>
    15]) !!}
</div>
<div class="form-group">
    {!! Form::label('categories', 'Categorías:') !!}
    {!! Form::select('categories', $lstCategory, null ,['class' =>'form-control required', 'multiple' => 'multiple']) !!}
</div>
<div class="form-group">
    {!! Form::label('owner_name', 'Propietario:') !!}
    {!! Form::text('owner_name', (isset($oOwner)) ?  $oOwner->names . ' ' . $oOwner->lastname : null, ['class' => 'form-control', 'maxlength' =>
    50]) !!}
    {!! Form::hidden('owner', (isset($oOwner)) ? $oOwner->id : null) !!}
</div>
<div class="form-group">
    {!! Form::label('breeder_name', 'Criador:') !!}
    {!! Form::text('breeder_name', (isset($oBreeder)) ? $oBreeder->names . ' ' . $oBreeder->lastname : null, ['class' => 'form-control', 'maxlength' =>
    50]) !!}
    {!! Form::hidden('breeder', (isset($oBreeder)) ? $oBreeder->id : null) !!}
</div>
<div class="form-group">
    {!! Form::label('prefix', 'Prefijo:') !!}
    {!! Form::text('prefix', (isset($oOwner)) ? $oOwner->prefix : null, ['class' => 'form-control', 'maxlength' =>
    50]) !!}
</div>


<select id="cat" name="multiselect[]" multiple="multiple">
    <option value="1">Option 1</option>
    <option value="2">Option 2</option>
    <option value="3">Option 3</option>
    <option value="4">Option 4</option>
    <option value="5">Option 5</option>
    <option value="6">Option 6</option>
</select>
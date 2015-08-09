<div class="form-group">
    {!! Form::label('description', 'Categoría:') !!}
    {!! Form::text('description', (isset($oCategory)) ? $oCategory->description : null, ['class' =>
    'form-control', 'maxlenght' => 200]) !!}
</div>
<div class="form-group">
    {!! Form::label('type', 'Selección:') !!}
    {!! Form::select('type', ['0' => 'No', '1' => 'Sí'], null ,['class' =>'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('count_competitors', 'Cantidad de Competidores:') !!}
    {!! Form::text('count_competitors', (isset($oCategory)) ? $oCategory->count_competitors : null, ['class'
    =>'form-control']) !!}
</div>
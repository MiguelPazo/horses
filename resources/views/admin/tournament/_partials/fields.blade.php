<div class="form-group">
    {!! Form::label('description', 'Concurso:') !!}
    {!! Form::text('description', (isset($oTournament)) ? $oTournament->description : null, ['class' =>
    'form-control', 'maxlenght' => 200]) !!}
</div>
<div class="form-group">
    {!! Form::label('date_begin', 'Fecha de Inicio:') !!}
    {!! Form::text('date_begin', (isset($oTournament)) ? $oTournament->date_begin : null, ['class' => 'datepicker
    form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('date_end', 'Fecha de Cierre:') !!}
    {!! Form::text('date_end', (isset($oTournament)) ? $oTournament->date_end : null, ['class' => 'datepicker
    form-control']) !!}
</div>
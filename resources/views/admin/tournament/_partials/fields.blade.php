<div class="form-group">
    {!! Form::label('description', 'Concurso:') !!}
    {!! Form::text('description', (isset($oTournament)) ? $oTournament->description : null, ['class' =>
    'form-control required', 'maxlength' => 200]) !!}
</div>
<div class="form-group">
    <div class="form-group">
        {!! Form::label('type', '¿Puntaje por promedio?:') !!}
        {!! Form::select('type', ['jury' => 'Sí', 'wjury' => 'No'], (isset($oTournament)) ? (($oTournament->type ==
        \Horses\Constants\ConstDb::TYPE_TOURNAMENT_JURY)? 'jury': 'wjury') : null ,['class' =>'form-control required'])
        !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('date_begin', 'Fecha de Inicio:') !!}
    {!! Form::text('date_begin', (isset($oTournament)) ? $oTournament->date_begin : null, ['class' => 'datepicker
    form-control required']) !!}
</div>
<div class="form-group">
    {!! Form::label('date_end', 'Fecha de Cierre:') !!}
    {!! Form::text('date_end', (isset($oTournament)) ? $oTournament->date_end : null, ['class' => 'datepicker
    form-control required']) !!}
</div>
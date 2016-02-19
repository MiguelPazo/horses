<div class="form-group">
    {!! Form::label('description', 'Categoría:') !!}
    {!! Form::text('description', (isset($oCategory)) ? $oCategory->description : null, ['class' =>
    'form-control required', 'maxlength' => 200]) !!}
</div>
<div class="form-group">
    {!! Form::label('type', 'Selección:') !!}
    {!! Form::select('type', ['1' => 'Sí', '0' => 'No'], (isset($oCategory)) ? (($oCategory->type ==
    \Horses\Constants\ConstDb::TYPE_CATEGORY_SELECTION)? '1': '0') : null ,['class' =>'form-control required']) !!}
</div>
<div class="form-group">
    {!! Form::label('mode', 'Modo:') !!}
    {!! Form::select('mode', [\Horses\Constants\ConstDb::MODE_PERSONAL => 'Personal',
     \Horses\Constants\ConstDb::MODE_GROUP => 'Grupal'], (isset($oCategory)) ? $oCategory->mode : null ,['class' =>'form-control required']) !!}
</div>
<div class="form-group">
    {!! Form::label('num_begin', 'Número del Primer Competidor:') !!}
    {!! Form::text('num_begin', (isset($oCategory)) ? $oCategory->num_begin : 1, ['class'
    =>'form-control integer required', 'maxlength' => 3]) !!}
</div>

<div class="row row_sorteable">
    <div class="col-md-6">
        <label> Jurados Registrados: </label>

        <div class="comp_list">
            <ul class="ul_comp_list">
                @foreach($lstJury as $jury)
                    <li>
                        <div class="btn btn-block btn-lg btn-primary">
                            {{ $jury->lastname . ',' . $jury->names }}
                            <input type="hidden" name="{{ \Horses\Constants\ConstApp::PREFIX_JURY . $jury->id }}"/>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>

    <div class="col-md-6">
        <label> Jurados de la Categoría: </label>

        <div class="comp_classify">
            <ul class="ul_comp_list">
                <?php $first = true ?>
                @foreach($lstJuryCategory as $juryCategory)
                    <li>
                        <div class="btn btn-block btn-lg btn-primary btn-success">
                            @if($first)
                                <span class="jury_diriment">Dirimente</span>
                            @endif
                            {{ $juryCategory->lastname . ',' . $juryCategory->names }}
                            <input type="hidden"
                                   name="{{ \Horses\Constants\ConstApp::PREFIX_JURY . $juryCategory->id }}"/>
                            <?php $first = false ?>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
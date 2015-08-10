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
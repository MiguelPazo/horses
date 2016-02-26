@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        {{ $oTournament->description }}
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            @foreach($lstCatalogGroup as $group)
                <h5><b>{{ $group[0]->description }}</b></h5>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th width="1%">N° de Cancha</th>
                        <th width="1%">N° de Catálogo</th>
                        <th width="1%">Prefijo</th>
                        <th width="5%">Nombre</th>
                        <th width="1%">Código</th>
                        <th width="1%">Fec/Nac</th>
                        <th width="1%">Pref. Padre</th>
                        <th width="5%">Nom. Padre</th>
                        <th width="1%">Pref. Madre</th>
                        <th width="5%">Nom. Madre</th>
                        <th width="10%">Criador</th>
                        <th width="10%">Propietario</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $pos = 0;
                    $actualGroup = null;
                    ?>
                    @foreach($group as $category)
                        <?php
                        if ($category->mode == \Horses\Constants\ConstDb::MODE_GROUP) {
                            if ($category->group != $actualGroup) {
                                $actualGroup = $category->group;
                                $pos++;
                            }
                        } else {
                            $pos++;
                        }
                        ?>
                        <tr>
                            <td>{{ $pos }}</td>
                            <td>{{ $category->number }}</td>
                            <td>{{ $category->prefix }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->code }}</td>
                            <td>{{ $category->birthdate }}</td>
                            <td>{{ $category->dad_prefix }}</td>
                            <td>{{ $category->dad_name }}</td>
                            <td>{{ $category->mom_prefix }}</td>
                            <td>{{ $category->mom_name }}</td>
                            <td>{{ $category->breeder }}</td>
                            <td>{{ $category->owner }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    </div>
@endsection

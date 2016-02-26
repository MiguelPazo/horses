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
                        <th>N° de Cancha</th>
                        <th>N° de Catálogo</th>
                        <th>Prefijo</th>
                        <th>Nombre</th>
                        <th>Código</th>
                        <th>Fec/Nac</th>
                        <th>Prefijo Padre</th>
                        <th>Nombre Padre</th>
                        <th>Prefijo Madre</th>
                        <th>Nombre Madre</th>
                        <th>Criador</th>
                        <th>Propietario</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $pos = 1 ?>
                    @foreach($group as $category)
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
                        <?php $pos++ ?>
                    @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>
    </div>
@endsection

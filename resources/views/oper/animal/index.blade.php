@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Catalogo para {{ $tournament }}
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <a href="{{ route('oper.animal.create') }}" class="btn btn-primary">Nuevo</a>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Prefijo</th>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Fecha de Nac.</th>
                    <th>Categorías</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1; ?>
                @foreach($lstAnimals as $animal)
                    <tr>
                        <td>{{ $count }}</td>
                        <td>prefix</td>
                        <td>{{ $animal->name }}</td>
                        <td>{{ $animal->code }}</td>
                        <td>{{ ($animal->birthdate != '')? DateTime::createFromFormat('Y-m-d H:i:s', $animal->birthdate)->format('d-m-Y'): '' }}</td>
                        <td>5</td>
                        <td>
                            <a href="{{ route('oper.animal.edit', $animal->id) }}" role="button"
                               class="btn">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>

                            <a href="{{ route('oper.animal.destroy', $animal->id) }}" role="button"
                               class="btn btn_link_prevent" data-method="delete">
                                <span class=" glyphicon glyphicon-trash"></span>
                            </a>
                        </td>
                    </tr>
                    <?php $count++; ?>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

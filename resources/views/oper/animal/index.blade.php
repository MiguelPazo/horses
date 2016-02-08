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
                        <?php $prefix = false ?>
                        @if($animal->agents->count() > 0)
                            @foreach($animal->agents as $agent)
                                @if($agent->pivot->type == \Horses\Constants\ConstDb::AGENT_BREEDER)
                                    <?php $prefix = true ?>
                                    <td>{{ $agent->prefix }}</td>
                                @endif
                            @endforeach
                        @endif
                        @if(!$prefix)
                            <td></td>
                        @endif

                        <td>{{ $animal->name }}</td>
                        <td>{{ $animal->code }}</td>
                        <td>{{ $animal->birthdate }}</td>
                        <td>{{ $animal->catalogs->count() }}</td>
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

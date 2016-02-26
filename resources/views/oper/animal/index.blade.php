@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Catalogo para {{ $oTournament->description }}
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <div class="form-inline">
                <label for="text_searched">Buscar: </label>
                <input id="text_searched" class="form-control" type="text" value="{{ $search }}">
                <a href="{{ route('oper.animal.index') }}" id="btn_search"
                   class="btn btn-primary glyphicon glyphicon-search"></a>
                <a href="{{ route('oper.animal.create') }}" class="btn btn-primary">Nuevo</a>
            </div>
            <p></p>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th width="1%">#</th>
                    <th width="10%">Prefijo</th>
                    <th width="20%">Nombre</th>
                    <th width="10%" class="center">Código</th>
                    <th width="15%" class="center">Fecha de Nac.</th>
                    <th width="5%" class="center">Categorías</th>
                    <th width="24%" class="center">Criador</th>
                    <th width="24%" class="center">Propietario</th>
                    <th width="15%" class="center">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = $lstAnimal->perPage() * ($lstAnimal->currentPage() - 1) + 1; ?>
                @foreach($lstAnimal as $animal)
                    <tr>
                        <td>{{ $count }}</td>
                        <td>{{ $animal->prefix }}</td>
                        <td>{{ $animal->name }}</td>
                        <td class="center">{{ $animal->code }}</td>
                        <td class="center">{{ $animal->birthdate }}</td>
                        <td class="center">{{ $animal->total_categories }}</td>
                        <td>{{ $animal->breeder }}</td>
                        <td>{{ $animal->owner }}</td>
                        <td class="center">
                            <a href="{{ route('oper.animal.edit', $animal->animal_id) }}" role="button"
                               class="btn">
                                <span class="glyphicon glyphicon-pencil"></span>
                            </a>

                            <a href="{{ route('oper.animal.destroy', $animal->animal_id) }}" role="button"
                               rel="¿Esta seguro que desea eliminar a {{ $animal->name }}?"
                               class="btn btn_link_prevent" data-method="delete">
                                <span class=" glyphicon glyphicon-trash"></span>
                            </a>
                        </td>
                    </tr>
                    <?php $count++; ?>
                @endforeach
                </tbody>
            </table>
            {!! str_replace('/?', '?', $lstAnimal->render()) !!}
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/js/app/oper/animal/index.js') }}"></script>
@endsection
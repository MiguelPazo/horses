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
                @foreach($oTournament->animals as $animal)
                    <tr>
                        <td>{{ $count }}</td>
                        <td>{{ ($animal->breeder->count() > 0)? $animal->breeder->get(0)->prefix: '' }}</td>
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

@section('scripts')
    <script src="{{ asset('/js/app/oper/animal/index.js') }}"></script>
@endsection
@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center text-primary">
                Listado de Categorias de {{ $oTournament->description }}
            </h3>

            <div class="panel-body">
                <div class="table-responsive">
                    <a href="{{ url('/admin/category/create', $oTournament->id) }}" class="btn btn-primary">Nuevo</a>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Categoria</th>
                            <th>Selección</th>
                            <th>Etapa Actual</th>
                            <th>Cantidad de Competidores</th>
                            <th>Opciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $count = 1; ?>
                        @foreach( $lstCategory as  $category)
                            <tr class="{{ ($category->status == \Horses\Constants\ConstDb::STATUS_ACTIVE)? 'info' : '' }}
                            {{ ($category->status == \Horses\Constants\ConstDb::STAGE_FINAL)? 'active' : '' }}">
                                <th scope="row">{{ $count }}</th>
                                <td>{{ $category->description }}</td>
                                <td>{{ ($category->type == \Horses\Constants\ConstDb::TYPE_CATEGORY_SELECTION)? 'Sí': 'No' }}</td>
                                <td>{{ $category->actual_stage }}</td>
                                <td>{{ $category->count_competitors }}</td>
                                <td>
                                    <a href="{{ url('/admin/category/enable', $category->id) }}" role="button"
                                       class="btn {{ ($category->status == \Horses\Constants\ConstDb::STATUS_ACTIVE)? 'btn-success' : '' }}">
                                        <span class="glyphicon glyphicon-star"></span>
                                    </a>

                                    <a href="{{ url('/admin/category/edit', $category->id) }}" role="button"
                                       class="btn">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                    </a>

                                    {{--<a href="{{ route('admin.category.destroy', $tournament->id) }}" role="button"--}}
                                    {{--class="btn" data-method="delete">--}}
                                    {{--<span class=" glyphicon glyphicon-trash"></span>--}}
                                    {{--</a>--}}
                                </td>
                            </tr>
                            <?php $count++; ?>
                        @endforeach
                        </tbody>
                    </table>
                    <a href="{{ url('/admin/category/create', $oTournament->id) }}" class="btn btn-primary">Nuevo</a>
                </div>
            </div>
        </div>
    </div>
@endsection

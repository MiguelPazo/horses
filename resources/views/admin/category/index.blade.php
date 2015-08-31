@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Listado de Categorias de {{ $oTournament->description }}
    </h3>

    @if($errorMessage != null)
        <div class="alert alert-warning alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">×</span>
            </button>
            <strong>Error!</strong> {{ $errorMessage }}
        </div>
    @endif

    <div class="panel-body">
        <div class="table-responsive">
            <a href="{{ route('admin.tournament.index') }}" role="button" class="btn btn-default">
                <span class="glyphicon glyphicon-menu-left"></span>
            </a>
            <a href="{{ url('/admin/category/create', $oTournament->id) }}" class="btn btn-primary">Nuevo</a>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Categoria</th>
                    <th>Selección</th>
                    <th>Etapa Actual</th>
                    <th>Cantidad de Competidores</th>
                    <th>Activa</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1; ?>
                @foreach( $lstCategory as  $category)
                    <tr class="{{ ($category->status == \Horses\Constants\ConstDb::STATUS_ACTIVE ||
                                    $category->status == \Horses\Constants\ConstDb::STATUS_IN_PROGRESS)? 'info' : '' }}
                            {{ ($category->status == \Horses\Constants\ConstDb::STATUS_FINAL)? 'active' : '' }}">
                        <th scope="row">{{ $count }}</th>
                        <td>{{ $category->description }}</td>
                        <td>{{ ($category->type == \Horses\Constants\ConstDb::TYPE_CATEGORY_SELECTION)? 'Sí': 'No' }}</td>
                        <td>
                            @if($category->status == \Horses\Constants\ConstDb::STATUS_ACTIVE &&
                                $category->actual_stage == null)
                                {{ \Horses\Constants\ConstApp::STAGE_ASSISTANCE }}
                            @elseif($category->actual_stage == \Horses\Constants\ConstDb::STAGE_ASSISTANCE)
                                {{ \Horses\Constants\ConstApp::STAGE_SELECCTION }}
                            @elseif($category->actual_stage == \Horses\Constants\ConstDb::STAGE_SELECTION)
                                {{ \Horses\Constants\ConstApp::STAGE_CLASSIFY_1 }}
                            @elseif($category->actual_stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                                {{ \Horses\Constants\ConstApp::STAGE_CLASSIFY_2 }}
                            @elseif($category->actual_stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_2)
                                {{ \Horses\Constants\ConstApp::STAGE_RESULTS }}
                            @endif
                        </td>
                        <td>{{ $category->count_competitors }}</td>
                        <td>
                            @if($category->status != \Horses\Constants\ConstDb::STATUS_FINAL)
                                @if($category->status == \Horses\Constants\ConstDb::STATUS_ACTIVE ||
                                    $category->status == \Horses\Constants\ConstDb::STATUS_IN_PROGRESS)
                                    <a href="{{ url('/admin/category/disable', $category->id ) }}" role="button"
                                       class="btn btn-success">
                                        <span class="glyphicon glyphicon-star"></span>
                                    </a>
                                @else
                                    <a href="{{ url('/admin/category/enable', $category->id ) }}" role="button"
                                       class="btn">
                                        <span class="glyphicon glyphicon-star"></span>
                                    </a>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($category->status == \Horses\Constants\ConstDb::STATUS_INACTIVE)
                                <a href="{{ url('/admin/category/edit', $category->id ) }}"
                                   role="button" class="btn">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>

                                <a href="{{ url('/admin/category/destroy', $category->id) }}"
                                   role="button" class="btn" data-method="delete">
                                    <span class=" glyphicon glyphicon-trash"></span>
                                </a>
                            @endif
                        </td>
                    </tr>
                    <?php $count++; ?>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

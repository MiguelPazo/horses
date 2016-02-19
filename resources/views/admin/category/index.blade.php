@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Listado de Categorias de {{ $oTournament->description }}
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <a href="{{ route('admin.tournament.index') }}" role="button" class="btn btn-default">
                <span class="glyphicon glyphicon-menu-left"></span>
            </a>
            @if($oTournament->status != \Horses\Constants\ConstDb::STATUS_FINAL)
                <a href="{{ url('/admin/category/create', $oTournament->id) }}" class="btn btn-primary">Nuevo</a>
            @endif
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th width="1%">#</th>
                    <th width="42%">Categoria</th>
                    <th width="8%" class="center">Selección</th>
                    <th width="10%" class="center">Modo</th>
                    <th width="14%" class="center">Etapa Actual</th>
                    <th width="17%" class="center">Cantidad de Competidores</th>
                    <th width="8%" class="center">Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = $lstCategory->perPage() * ($lstCategory->currentPage() - 1) + 1; ?>
                @foreach( $lstCategory as  $category)
                    <tr class="{{ ($category->status == \Horses\Constants\ConstDb::STATUS_ACTIVE)? 'info' : '' }}
                    {{ ($category->status == \Horses\Constants\ConstDb::STATUS_FINAL)? 'active' : '' }}">
                        <th scope="row">{{ $count }}</th>
                        <td>{{ $category->description }}</td>
                        <td class="center">{{ ($category->type == \Horses\Constants\ConstDb::TYPE_CATEGORY_SELECTION)? 'Sí': 'No' }}</td>
                        <td class="center">{{ ($category->mode == \Horses\Constants\ConstDb::MODE_PERSONAL)? 'Personal': 'Grupal' }}</td>
                        <td class="center">
                            @if($category->status == \Horses\Constants\ConstDb::STATUS_INACTIVE &&
                                $category->actual_stage == \Horses\Constants\ConstDb::STAGE_ASSISTANCE)
                                {{ \Horses\Constants\ConstApp::STAGE_ASSISTANCE }}
                            @elseif($category->actual_stage == \Horses\Constants\ConstDb::STAGE_ASSISTANCE)
                                {{ \Horses\Constants\ConstApp::STAGE_SELECCTION }}
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
                        <td class="center">{{ $category->count_competitors }}</td>
                        <td class="center">
                            @if($oTournament->status != \Horses\Constants\ConstDb::STATUS_FINAL)
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
                            @endif
                        </td>
                    </tr>
                    <?php $count++; ?>
                @endforeach
                </tbody>
            </table>
            {!! str_replace('/?', '?', $lstCategory->render()) !!}
        </div>
    </div>
@endsection

@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Listado de Categorias de {{ $oTournament->description }}
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Categoria</th>
                    <th>Selección</th>
                    <th>Modo</th>
                    <th>Etapa Actual</th>
                    <th>Comp. Inscritos</th>
                    <th>Comp. Presentes</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1; ?>
                @foreach( $lstCategory as  $category)
                    <tr class="{{ ($category->status == \Horses\Constants\ConstDb::STATUS_ACTIVE)? 'info' : '' }}
                    {{ ($category->status == \Horses\Constants\ConstDb::STATUS_FINAL)? 'active' : '' }}">
                        <th scope="row">{{ $count }}</th>
                        <td>{{ $category->description }}</td>
                        <td>{{ ($category->type == \Horses\Constants\ConstDb::TYPE_CATEGORY_SELECTION)? 'Sí': 'No' }}</td>
                        <td>{{ ($category->mode == \Horses\Constants\ConstDb::MODE_PERSONAL)? 'Personal': 'Grupal' }}</td>
                        <td>
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
                        <td>{{ $category->count_competitors }}</td>
                        <td>{{ $category->count_presents }}</td>
                        <td>
                            @if($category->status == \Horses\Constants\ConstDb::STATUS_INACTIVE)
                                <a href="{{ url('/commissar/enable', $category->id ) }}" role="button"
                                   class="btn btn_star">
                                    <span class="glyphicon glyphicon-star"></span>
                                </a>

                                <a href="{{ route('commissar.assistance', $category->id) }}"
                                   role="button" class="btn">
                                    <span class="glyphicon glyphicon-user"></span>
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

@section('scripts')
    <script src="{{ asset('/js/app/commissar/index.js') }}"></script>
@endsection

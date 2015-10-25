@extends('layout')

@section('content')
    <h3 class="text-center text-primary">
        Listado de Usuarios
    </h3>

    <div class="panel-body">
        <div class="table-responsive">
            <a href="{{ route('admin.user.create') }}" class="btn btn-primary">Nuevo</a>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>Conectado</th>
                    <th>Perfil</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?php $count = 1; ?>
                @foreach( $lstUser as  $user)
                    <tr>
                        <th scope="row">{{ $count }}</th>
                        <td>{{ $user->user }}</td>
                        <td>{{ $user->lastname }}</td>
                        <td>{{ $user->names }}</td>
                        <td>
                            @if($user->profile != \Horses\Constants\ConstDb::PROFILE_ADMIN)
                                @if ($user->login == \Horses\Constants\ConstDb::USER_CONECTED)
                                    <span class="glyphicon glyphicon-ok-sign btn text-success btn"></span>
                                @else
                                    <span class="glyphicon glyphicon-minus-sign btn text-danger btn"></span>
                                @endif
                            @else
                                <span class="glyphicon glyphicon-minus btn text-success btn"></span>
                            @endif
                        </td>
                        <td>
                            @if($user->profile == \Horses\Constants\ConstDb::PROFILE_ADMIN)
                                Administrador
                            @elseif($user->profile == \Horses\Constants\ConstDb::PROFILE_COMMISSAR)
                                Comisario
                            @else
                                Jurado
                            @endif
                        </td>
                        <td>
                            @if($user->profile != \Horses\Constants\ConstDb::PROFILE_ADMIN)
                                <a href="{{ route('admin.user.edit', $user->id) }}" role="button" class="btn">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>

                                @if ($user->login == \Horses\Constants\ConstDb::USER_CONECTED)
                                    <a href="{{ route('admin.user.unlock', $user->id) }}" role="button" class="btn">
                                        <span class="glyphicon glyphicon-off"></span>
                                    </a>
                                @endif

                                <a href="{{ route('admin.user.destroy', $user->id) }}" role="button"
                                   class="btn" data-method="delete">
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

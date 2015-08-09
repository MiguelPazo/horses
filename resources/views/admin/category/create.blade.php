@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center text-primary">
                Nueva Categoría para {{ $oTournament->description }}
            </h3>

            <div class="panel-body">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {!! Form::open(['url' => ['/admin/category/store', $oTournament->id ]]) !!}
                @include('admin.category.partials.fields')
                {!! Form::submit('Guardar', ['class' => 'btn btn-default']) !!}

                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script src="{{ asset('/js/admin.js') }}"></script>
@endsection

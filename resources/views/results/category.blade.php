@extends('layout', ['classResult' => (!Auth::check()) ? 'black-ground' : ''])

@section('content')
    @include('results._partials.header')
    <div class="col-md-10">
        <h4 class="label_category">Categoría: {{ $oCategory->description}}</h4>

        <div class="col-md-12">
            @include('results._partials.result')
            @if(Auth::check())
                <button class="btn btn-success btn_print">IMPRIMIR</button>
            @endif
        </div>
    </div>
@endsection


@section('scripts')
    @if(!Auth::check())
        <script src="{{ asset('/js/app/results.js') }}"></script>
    @endif
@endsection

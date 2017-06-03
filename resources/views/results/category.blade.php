@extends('layout', ['classResult' => (!Auth::check()) ? 'black-ground' : ''])

<?php
$withoutBar = (!Auth::check() && env('WITHOUTSIDE')) ? true : false;
?>

@section('content')

    @include('results._partials.header', ['withoutBar' => $withoutBar])

    <div class="{{ ($withoutBar)? 'col-md-12':'col-md-10' }}">
        <h4 class="label_category">Categoría: {{ $oCategory->description}}</h4>

        @if($withoutBar)
            <h3 class="hide-print">Categoría: <b>{{ $oCategory->description}}</b></h3>
        @endif

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

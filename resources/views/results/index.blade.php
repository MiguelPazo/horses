@extends('layout', ['classResult' => (!Auth::check()) ? 'black-ground' : ''])

<?php
$withoutBar = (!Auth::check() && env('WITHOUTSIDE')) ? true : false;
?>

@section('content')

    @include('results._partials.header', ['withoutBar' => $withoutBar])

    <div class="{{ ($withoutBar)? 'col-md-12':'col-md-10' }}">
    </div>

@endsection

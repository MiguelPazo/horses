@extends('layout', ['classResult' => (!Auth::check()) ? 'black-ground' : ''])

@section('content')
    @include('results._partials.header')
    <div class="col-md-10">
    </div>

@endsection

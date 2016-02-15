@extends('layout')

@section('content')
    <h5><b>Concurso: {{ $oTournament->description }}</b></h5>
    @if($wData)
        <h3 class="text-center text-primary">
            {{ $oCategory->description}}
        </h3>
        <div class="col-md-12">
            @if($show)
                @include('results._partials.result')
            @endif
        </div>
        @if($suggest != 0)
            <a class="suggest btn btn-success btn-lg" href="{{ url('/general-commissar', $suggest) }}">
                Ver Siguiente <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
        @endif
    @endif
@endsection


@section('scripts')
    <script src="{{ asset('/js/app/results.js') }}"></script>
@endsection

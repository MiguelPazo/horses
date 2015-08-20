@extends('layout')

@section('content')
    @include('results._partials.header')
    <div class="col-md-10">
        <div class="col-md-12">
            <div class="tab-pane" id="pane_stage">
                <div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#winers" aria-controls="home" role="tab" data-toggle="tab">Ganadores</a>
                        </li>
                        <li role="presentation">
                            <a href="#honorables" aria-controls="profile" role="tab" data-toggle="tab">Premios
                                Honrosos</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="winers">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Puesto</th>
                                    <th>Número de Participante</th>
                                    <th>Puntaje</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $position = 1; ?>
                                @foreach($lstCompetitorLeft as $competitor)
                                    <tr>
                                        <th scope="row">{{ $position }}</th>
                                        <td>{{ str_pad($competitor->number, 2, "0", STR_PAD_LEFT) }}</td>
                                        <td>{{ $competitor->points }}</td>
                                    </tr>
                                    <?php $position++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div role="tabpanel" class="tab-pane fade" id="honorables">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Puesto</th>
                                    <th>Número de Participante</th>
                                    <th>Puntaje</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $position = 7; ?>
                                @foreach($lstCompetitorRight as $competitor)
                                    <tr>
                                        <th scope="row">{{ $position }}</th>
                                        <td>{{ str_pad($competitor->number, 2, "0", STR_PAD_LEFT) }}</td>
                                        <td>{{ $competitor->points }}</td>
                                    </tr>
                                    <?php $position++; ?>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

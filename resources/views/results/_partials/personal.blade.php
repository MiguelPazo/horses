<?php
$position = 1;
$bigNumber = (!Auth::check()) ? 'big-number' : '';
?>

@foreach($lstCompetitorWinners as $competitor)
    <tr>
        @if($limp)
            <td scope="row">
                <a href="{{ url('/general-commissar/category/' . $competitor->category_id . '/limp/' . $competitor->id) }}"
                   class="btn_link_prevent"
                   rel="¿Esta seguro que desea claudicar al competiro con número {{ $competitor->number }}?">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </td>
        @endif
        <td scope="row">{{ $position }}</td>
        <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
        @if($complete)
            <td scope="row">{{ $competitor->catalog }}</td>
            <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->prefix:'' }}</td>
            <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->name:'' }}</td>
            <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->owner:'' }}</td>
        @endif
        <?php $acum = 0 ?>
        @foreach($competitor->stages as $stage)
            @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                <td class="center {{ $bigNumber }} {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active active-diriment':'' }}">{{ $stage->position }}</td>
                <?php $acum += $stage->position ?>
            @endif
        @endforeach
        <td class="center {{ $bigNumber }} result-final">{{ $acum }}</td>

        @if($showSecond)
            <?php $acum = 0 ?>
            @foreach($competitor->stages as $stage)
                @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_2)
                    <td class="center {{ $bigNumber }} {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active active-diriment':'' }}">{{ $stage->position }}</td>
                    <?php $acum += $stage->position ?>
                @endif
            @endforeach
            <td class="center {{ $bigNumber }} result-final">{{ $acum }}</td>
        @endif
    </tr>
    <?php $position++; ?>
@endforeach

<?php $position = 7; ?>
@foreach($lstCompetitorHonorable as $competitor)
    <tr>
        @if($limp)
            <td></td>
        @endif
        <td scope="row">{{ $position }}</td>
        <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
        @if($complete)
            <td scope="row">{{ $competitor->catalog }}</td>
            <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->prefix:'' }}</td>
            <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->name:'' }}</td>
            <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->owner:'' }}</td>
        @endif
        <?php $acum = 0 ?>
        @foreach($competitor->stages as $stage)
            @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                <td class="center {{ $bigNumber }} {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active active-diriment':'' }}">{{ $stage->position }}</td>
                <?php $acum += $stage->position ?>
            @endif
        @endforeach
        <td class="center {{ $bigNumber }} result-final">{{ $acum }}</td>
    </tr>
    <?php $position++; ?>
@endforeach
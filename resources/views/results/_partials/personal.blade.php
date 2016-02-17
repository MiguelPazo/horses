<?php $position = 1; ?>
@foreach($lstCompetitorWinners as $competitor)
    <tr>
        <td scope="row">{{ $position }}</td>
        <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
        <td scope="row">{{ $competitor->catalog }}</td>
        <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->prefix:'' }}</td>
        <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->name:'' }}</td>
        <?php $acum = 0 ?>
        @foreach($competitor->stages as $stage)
            @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                <?php $acum += $stage->position ?>
            @endif
        @endforeach
        <td class="center success">{{ $acum }}</td>

        @if($showSecond)
            <?php $acum = 0 ?>
            @foreach($competitor->stages as $stage)
                @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_2)
                    <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                    <?php $acum += $stage->position ?>
                @endif
            @endforeach
            <td class="center success">{{ $acum }}</td>
        @endif
    </tr>
    <?php $position++; ?>
@endforeach

<?php $position = 1; ?>
@foreach($lstCompetitorHonorable as $competitor)
    <tr>
        <td scope="row">MH{{ $position }}</td>
        <td>{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
        <td scope="row">{{ $competitor->catalog }}</td>
        <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->prefix:'' }}</td>
        <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->name:'' }}</td>
        <?php $acum = 0 ?>
        @foreach($competitor->stages as $stage)
            @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                <?php $acum += $stage->position ?>
            @endif
        @endforeach
        <td class="center success">{{ $acum }}</td>
    </tr>
    <?php $position++; ?>
@endforeach
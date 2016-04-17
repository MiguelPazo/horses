<?php $position = 1; ?>
@foreach($lstCompetitorWinners as $lstCompetitor)
    <?php $print = true; ?>
    @foreach($lstCompetitor as $competitor)
        <tr>
            @if($print)
                @if($limp)
                    <td scope="row">
                        <a href="{{ url('/general-commissar/category/' . $competitor->category_id . '/limp/' . $competitor->id) }}"
                           class="btn_link_prevent"
                           rel="¿Esta seguro que desea claudicar al competiro con número {{ $competitor->number }}?">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    </td>
                @endif
                <td rowspan="{{ ($complete) ? $lstCompetitor->count() : '' }}" scope="row">{{ $position }}</td>
                <td rowspan="{{ ($complete) ? $lstCompetitor->count() : '' }}">{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
            @endif
            @if($complete)
                <td scope="row">{{ $competitor->catalog }}</td>
                <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->prefix:'' }}</td>
                <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->name:'' }} </td>
                <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->owner:'' }}</td>
            @endif
            <?php $acum = 0 ?>
            @foreach($competitor->stages as $stage)
                @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                    <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                    <?php $acum += $stage->position ?>
                @endif
            @endforeach
            <td class="center result-final">{{ $acum }}</td>

            @if($showSecond)
                <?php $acum = 0 ?>
                @foreach($competitor->stages as $stage)
                    @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_2)
                        <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                        <?php $acum += $stage->position ?>
                    @endif
                @endforeach
                <td class="center result-final">{{ $acum }}</td>
            @endif
        </tr>
        <?php
        $print = false;
        if (!$complete) {
            break;
        }
        ?>
    @endforeach
    <?php $position++; ?>
@endforeach

<?php $position = 7; ?>
@foreach($lstCompetitorHonorable as $lstCompetitor)
    <?php $print = true; ?>
    @foreach($lstCompetitor as $competitor)
        <tr>
            @if($print)
                @if($limp)
                    <td></td>
                @endif
                <td rowspan="{{ ($complete) ? $lstCompetitor->count() : '' }}" scope="row">{{ $position }}</td>
                <td rowspan="{{ ($complete) ? $lstCompetitor->count() : '' }}">{{ str_pad($competitor->number, $lenCompNum, "0", STR_PAD_LEFT) }}</td>
            @endif
            @if($complete)
                <td scope="row">{{ $competitor->catalog }}</td>
                <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->prefix:'' }}</td>
                <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->name:'' }}</td>
                <td scope="row">{{ isset($competitor->animal_details)? $competitor->animal_details->owner:'' }}</td>
            @endif
            <?php $acum = 0 ?>
            @foreach($competitor->stages as $stage)
                @if($stage->stage == \Horses\Constants\ConstDb::STAGE_CLASSIFY_1)
                    <td class="center {{ ($stage->jury->id == $juryDiriment->user_id) ? 'active':'' }}">{{ $stage->position }}</td>
                    <?php $acum += $stage->position ?>
                @endif
            @endforeach
            <td class="center result-final">{{ $acum }}</td>
        </tr>
        <?php $print = false; ?>
    @endforeach
    <?php $position++; ?>
@endforeach
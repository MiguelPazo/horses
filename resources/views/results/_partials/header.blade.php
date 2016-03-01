<h3 class="text-center text-primary">
    {{ $oTournament->description }}
</h3>

<div class="col-md-2">
    <ul class="nav nav-pills nav-stacked">
        @foreach($lstCategory as $category)
            <li class="{{ (isset($oCategory))? ($category->id == $oCategory->id) ? 'active':'' : '' }}">
                @if($complete)
                    <a href="{{ route('tournament.results.category', [$oTournament->id, $category->id]) }}">
                        {{ $category->description }}
                    </a>
                @else
                    <a href="{{ url('general-commissar/category/'. $category->id) }}">
                        {{ $category->description }}
                    </a>
                @endif
            </li>
        @endforeach
    </ul>

</div>
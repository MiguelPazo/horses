<h3 class="text-center text-primary">
    {{ $oTournament->description }}
</h3>

<div class="col-md-2">
    <ul class="nav nav-pills nav-stacked">
        @foreach($lstCategory as $category)
            @if(isset($active))
                @if($category->id == $active)
                    <li class="active">
                        <a href="{{ route('tournament.results.category', $category->id) }}">
                            {{ $category->description }}
                        </a>
                    </li>
                @else
                    <li class="">
                        <a href="{{ route('tournament.results.category', $category->id) }}">
                            {{ $category->description }}
                        </a>
                    </li>
                @endif
            @else
                <li class="">
                    <a href="{{ route('tournament.results.category', $category->id) }}">
                        {{ $category->description }}
                    </a>
                </li>
            @endif

        @endforeach
    </ul>

</div>
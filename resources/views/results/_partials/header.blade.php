<h3 class="text-center text-primary">
    {{ $oTournament->description }}
</h3>

<div class="col-md-2">
    <ul class="nav nav-pills nav-stacked">
        @foreach($lstCategory as $category)
            <li class="{{ (isset($oCategory))? ($category->id == $oCategory->id) ? 'active':'' : '' }}">
                <a href="{{ route('tournament.results.category', [$oTournament->id, $category->id]) }}">
                    {{ $category->description }}
                </a>
            </li>
        @endforeach
    </ul>

</div>

<script src="{{ asset('/js/app/results.js') }}"></script>
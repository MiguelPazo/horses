<h3 class="text-center text-primary">
    {{ Session::get('oTournament')->description }}
</h3>

<div class="title">
    <span> Bienvenido: {{ Auth::user()->lastname .  ', ' . Auth::user()->names }} {{ Session::get('diriment') ? '(Juez Dirimente)':''  }}</span>
</div>

<p><b>Categoria: </b> {{ Session::get('oCategory')->description }}</p>

<p><b>Etapa: </b> {{ $stage }}</p>

@if($valid)
    <a role="button" class="btn btn-danger" id="btn_close_step">
        CERRAR ETAPA
    </a>
@endif
<h3 class="text-center text-primary">
    {{ Session::get('oCategory')->description }}
</h3>

<div class="title">
    <span> Bienvenido: {{ Auth::user()->lastname .  ', ' . Auth::user()->names }} {{ Session::get('diriment') ? '(Juez Dirimente)':''  }}</span>
</div>

<p><b>Concurso: </b> {{ Session::get('oTournament')->description }}</p>

<p><b>Etapa: </b> {{ $stage }}</p>

<a role="button" class="btn btn-primary btn_disable" id="btn_change_cat">
    CAMBIAR DE CATEGORÍA
</a>

@if($valid)
    <a role="button" class="btn btn-danger btn_disable" id="btn_close_step">
        CERRAR ETAPA
    </a>
@endif
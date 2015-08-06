<div class="title">
    <span> Bienvenido: {{ Auth::user()->nombres }} {{ Session::get('dirimente') ? '(Juez Dirimente)':''  }}</span>
    <a href="{{ url('auth/logout') }}" role="button" class="btn btn-danger">SALIR</a>
</div>
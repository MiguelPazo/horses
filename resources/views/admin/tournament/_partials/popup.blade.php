<div class="modal fade" id="modal_catalog_generating" role="dialog"
     aria-labelledby="modal_max_select_label" aria-hidden="true" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Catálogo </h4>
            </div>
            <div class="modal-body loading">
                <img src="{{ asset('/img/loading.gif') }}" alt="loading">
                <span>Generando el catálogo...</span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_catalog_generated" role="dialog"
     aria-labelledby="modal_max_select_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Catálogo </h4>
            </div>
            <div class="modal-body">
                El catálogo para este concurso ya ha sido generado, usted puede re-generarlo o visualizarlo.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn_disable btn-danger" id="btn_gen_catalog">
                    Re-Generar
                </button>
                <a target="_blank" role="button" class="btn btn-default btn-success btn_disable" id="btn_view_catalog">
                    Ver Catálogo
                </a>
                <button type="button" class="btn btn-default btn_disable" data-dismiss="modal">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
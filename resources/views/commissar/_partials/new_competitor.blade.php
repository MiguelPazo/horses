<div class="modal fade" id="modal_new_animal" role="dialog"
     aria-labelledby="modal_max_select_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true"> ×
                </button>
                <h4 class="modal-title"> Nuevo Competidor </h4>
            </div>
            <div class="modal-body">
                <div id="step_1">
                    <div class="form-group">
                        <label for="num_catalog">Número de Catalogo:</label>
                        <input type="text" class="form-control integer" id="num_catalog" name="num_catalog"/>
                    </div>
                </div>

                <div id="step_2">
                    @include('commissar._partials.form')
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn_disable" id="btn_next">Continuar</button>
                <button type="button" class="btn btn-default btn_disable" data-dismiss="modal">
                    Cancelar
                </button>
            </div>

        </div>
    </div>
</div>
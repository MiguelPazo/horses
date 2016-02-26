$(document).ready(function () {
    var tournament = 0;

    $('.datepicker').datepicker({
        'dateFormat': 'dd-mm-yy',
        showButtonPanel: true
    });

    $('.btn_catalog').click(function (e) {
        e.preventDefault();

        tournament = $(this).attr('rel');
        disableButtons(true);

        $.get(BASE_URL + 'admin/catalog/verify/' + tournament, null, function (response) {
            disableButtons(false);

            if (response) {
                var link = BASE_URL + 'catalog/report/' + tournament;
                $('#btn_view_catalog').attr('href', link);
                $('#modal_catalog_generated').modal('show');
            } else {
                generatingCatalog(tournament);
            }
        });
    });

    $('#btn_view_catalog').click(function (e) {
        e.preventDefault();
        $('.modal').modal('hide');

        var win = window.open($(this).attr('href'), '_blank');
        win.focus();
    });

    $('#btn_gen_catalog').click(function () {
        generatingCatalog(tournament);
    });

    var generatingCatalog = function (tournament) {
        $('.modal').modal('hide');
        $('#modal_catalog_generating').modal('show');
        includeBack();

        disableButtons(true);

        $.get(BASE_URL + 'admin/catalog/assign/' + tournament, null, function (response) {
            disableButtons(false);
            $('.modal').modal('hide');

            if (response.success) {
                openPopup('Información', 'Catalogo generado satisfactoriamente', 1, null);
                includeBack();
            } else {
                openPopup('Error', response.message, 1, null);
            }
        });
    };

    var includeBack = function () {
        $('body').append('<div class="modal-backdrop fade in"></div>');
        $('#modal_catalog_generating').on('hidden.bs.modal', function (e) {
            $('.modal-backdrop').remove();
        });
    };
});
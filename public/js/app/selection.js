$(document).ready(function () {
    var totalSelected = 0;

    var prepareForm = function () {
        $('#pane_stage .btn_competitor').each(function (i, e) {
            if ($(e).hasClass('btn-success')) {
                $(e).next('input').val('1');
            }
        });
    };

    $('#pane_stage .btn_competitor').click(function () {
        if ($(this).hasClass('btn-success')) {
            totalSelected--;
            $(this).removeClass('btn-success');
        } else {
            if (totalSelected < 12) {
                totalSelected++;
                $(this).addClass('btn-success');
            } else {
                openPopup('Error', 'Usted ya ha seleccionado a 12 concursantes!', 1, null);
            }
        }

        $('#count_sel').html(totalSelected);
    });

    $('#form_pane').submit(function (e) {
        e.preventDefault();
        disableButtons(true);
        prepareForm();
        $('#process').val('2')

        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            data: data,
            success: function (response) {
                if (response.success) {
                    if (response.url != '') {
                        location.href = response.url;
                    } else {
                        openPopup('Información', 'Información guardada satisfactoriamente!', 1, null);
                    }
                } else {
                    openPopup('Error', response.message, 1, null);
                }
            },
            error: function (response) {
                generalError();
            }
        });
    });

    $('#btn_close_step').click(function () {
        openPopup('Adventencia', 'Al cerrar la etapa no podrá volver a modificar los resultados, ¿Esta seguro?', 2, function () {
            $('#form_pane').submit();
        });
    });
});
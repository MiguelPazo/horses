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
                $('#modal_message').html('Usted ya ha seleccionado a 12 concursantes!');
                $('#modal_max_select').modal('show');
            }
        }

        $('#count_sel').html(totalSelected);
    });

    $('#btn_end').click(function () {
        prepareForm();
        $('#process').val('2')
        $('#form_pane').submit();
    });

    $('#form_pane').submit(function (e) {
        e.preventDefault();

        var url = $(this).attr('action');
        var data = $(this).serialize();

        $.get(url, data, function (response) {
            if (response.success) {
                if (response.url != '') {
                    location.href = response.url;
                } else {
                    alert('Información guardada satisfactoriamente');
                }
            } else {
                alert(response.message);
            }
        });
    });

    $('#close_stage').click(function () {
        $('#modal-container').modal('show');
    });
});
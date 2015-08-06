$(document).ready(function () {

    var prepareForm = function () {
        $('#pane_stage .btn_competitor').each(function (i, e) {
            if ($(e).hasClass('btn-success')) {
                $(e).next('input').val('1');
            }
        });
    };

    $('#pane_stage .btn_competitor').click(function () {
        var count = 0;

        $('#pane_stage .btn_competitor').each(function (i, e) {
            if ($(e).hasClass('btn-success')) {
                count++;
            }
        });

        if ($(this).hasClass('btn-success')) {
            $(this).removeClass('btn-success');
        } else {
            if (count < 12) {
                $(this).addClass('btn-success');
            } else {
                alert('Usted ya ha seleccionado a 12 participantes!');
            }
        }
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
});
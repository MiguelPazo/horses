$(document).ready(function () {

    var prepareForm = function () {
        $('#pane_stage .btn_competitor').each(function (i, e) {
            if ($(e).hasClass('btn-success')) {
                $(e).next('input').val('1');
            } else {
                $(e).next('input').val('0');
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
            $(this).addClass('btn-success');
        }
    });

    $('#btn_confirm').click(function () {
        $('#form').submit();
    });

    $('#form').submit(function (e) {
        prepareForm();
    });
});
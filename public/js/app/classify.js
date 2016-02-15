$(document).ready(function () {
    var countConfirm = 0;
    var countNeedConfirm = 0;

    var prepareForm = function () {
        var position = 1;

        $('.comp_classify').find('li').each(function (i, e) {
            $(e).find('input').val(position);
            position++;
        });
    };

    var fixPositions = function () {
        var position = 1;
        var countFClassify = 0;
        var countClassify = 0;

        $('.comp_classify').find('li').each(function (i, e) {
            $(e).find('.comp_position').remove();
            $(e).find('div').prepend('<span class="comp_position">' + position + '</span>');
            position++;
            countClassify++;
        });

        $('.comp_list').find('li').each(function (i, e) {
            $(e).find('.comp_position').remove();
            countFClassify++;
        });

        $('#count_fclassify').text(countFClassify);
        $('#count_classify').text(countClassify);
    };

    $(".ul_comp_list").sortable({
        connectWith: '.ul_comp_list',
        placeholder: 'placeholder'
    }).on('sortstop', function (e, ui) {
        var element = ui.item;
        var prevDiv = element.find('div');

        if (element.parent().parent().hasClass('comp_classify')) {
            prevDiv.addClass('btn-success');
        } else {
            prevDiv.removeClass('btn-success');
        }

        fixPositions();
    });

    $('#form_pane').submit(function (e) {
        e.preventDefault();
        disableButtons(true);
        prepareForm();
        $('#process').val('2');

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
                    openPopup('Error', response.message, 1, null)
                }
            },
            error: function (response) {
                generalError();
            }
        });
    });

    $('#btn_close_step').click(function () {
        var countUnclassify = $('.comp_list').find('li').length;
        countConfirm = 0;
        countNeedConfirm = $('.comp_classify').find('li').length;
        $('#btn_confirm').attr('disabled', 'disabled');

        if (countUnclassify == 0) {
            var html = $('.comp_classify').html().replace(/btn-success/g, '');

            $('#space_verify').html(html);
            $('#modal_verify').modal('show');
        } else {
            openPopup('Error', 'Debe clasificar a todos los concursantes!', 1, null);
        }
    });

    $('#space_verify').on('click', '.btn', function () {
        if ($(this).hasClass('btn-success')) {
            countConfirm--;
            $(this).removeClass('btn-success');
        } else {
            countConfirm++;
            $(this).addClass('btn-success');
        }

        if (countConfirm == countNeedConfirm) {
            $('#btn_confirm').removeAttr('disabled');
        } else {
            $('#btn_confirm').attr('disabled', 'disabled');
        }
    });

    $('#btn_confirm').click(function () {
        $('#modal_verify').modal('hide');

        openPopup('Adventencia', 'Al cerrar la etapa no podrá volver a modificar los resultados, ¿Esta seguro?', 2, function () {
            $('#form_pane').submit();
        });
    });


    fixPositions();
});
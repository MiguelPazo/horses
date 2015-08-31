$(document).ready(function () {
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
        var countUnclassify = $('.comp_list').find('li').length;

        if (countUnclassify == 0) {
            $('#modal-container').modal('show');
        } else {
            $('#modal_message').html('Debe clasificar a todos los concursantes!');
            $('#modal_max_select').modal('show');
        }

    });

    fixPositions();
});
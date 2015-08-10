$(document).ready(function () {
    var prepareForm = function () {
        var position = 1;

        $('.comp_classify').find('li').each(function (i, e) {
            $(e).find('input').val(position);
            position++;
        });

        $('.comp_list').find('li').each(function (i, e) {
            $(e).find('input').val(0);
        });
    };

    var fixPositions = function () {
        var position = 0;

        $('.comp_classify').find('li').each(function (i, e) {
            $(e).find('.comp_position').remove();
            $(e).find('div').prepend('<span class="comp_position">' + position + '</span>');
            position++;
        });

        $('.comp_list').find('.comp_position').each(function (i, e) {
            $(e).remove();
        });
    };

    $(".ul_comp_list").sortable({
        connectWith: '.ul_comp_list',
        placeholder: 'placeholder'
    }).on('sortreceive', function (e, ui) {
        var element = ui.item;
        var prevDiv = element.find('div');

        if (element.parent().parent().hasClass('comp_classify')) {
            prevDiv.addClass('btn-success');
        } else {
            prevDiv.removeClass('btn-success');
        }
    }).on('sortout', function (e, ui) {
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
});
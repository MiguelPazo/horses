$(document).ready(function () {

    var fixDiriment = function () {
        var findDiriment = false;

        $('.comp_classify').find('li').each(function (i, e) {
            $(e).find('.jury_diriment').remove();

            if (!findDiriment) {
                $(e).find('div').prepend('<span class="jury_diriment">Dirimente</span>');
                findDiriment = true;
            }
        });

        $('.comp_list').find('li').each(function (i, e) {
            $(e).find('.jury_diriment').remove();
        });
    };

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

    $(".datepicker").datepicker({
        'dateFormat': 'dd-mm-yy',
        showButtonPanel: true
    });

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

        fixDiriment();
    }).on('sortout', function (e, ui) {
        fixDiriment();
    });

    $('#form').submit(function (e) {
        prepareForm();
    });
});
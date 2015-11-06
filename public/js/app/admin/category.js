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

        fixDiriment();
    });

    $('#form').submit(function (e) {
        prepareForm();
    });

    $('#formCategory').submit(function (e) {
        e.preventDefault();
        prepareForm();

        var form = $(this);
        var method = form.attr('method');
        var methodElement = form.find("input[name='_method']");

        if (methodElement.length != 0) {
            method = methodElement.val();
        }

        if (validateForm(form)) {
            $.ajax({
                url: form.attr('action'),
                method: method,
                dataType: 'json',
                data: form.serialize(),
                success: function (response) {
                    if (response.success) {
                        location.href = response.url;
                    } else {
                        openPopup('Error', response.message, 1, null);
                    }
                },
                error: function (response) {
                    openPopup('Error', 'Ha ocurrido un error, se recargará la página', 1, null);

                    reloadPage();
                }
            });
        } else {
            openPopup('Error', 'Debe llenar todos los campos', 1, null);
        }
    });
});
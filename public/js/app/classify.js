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

    $('#form_pane').submit(function (e) {
        e.preventDefault();
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
            error: function (response){
                openPopup('Error', 'Ha ocurrido un error, se recargará la página', 1, null);

                setTimeout(function(){
                    location.reload();
                },2000);
            }
        });
    });

     $('#btn_confirm').click(function () {
        var countUnclassify = $('.comp_list').find('li').length;

        if (countUnclassify == 0) {
            openPopup('Adventencia', 'Al cerrar la etapa no podrá volver a modificar los resultados, ¿Esta usted seguro?', 2, function(){
                $('#form_pane').submit();
            });
        } else {
            openPopup('Error', 'Debe clasificar a todos los concursantes!', 1, null);
        }
    });

    fixPositions();
});
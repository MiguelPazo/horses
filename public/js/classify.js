$(document).ready(function () {
    var prepareForm = function () {
        var position = 0;

        $('.comp_classify').find('li').each(function (i, e) {
            $(e).find('input').val(position);
            position++;
        });
    };

    $(".ul_comp_list").sortable({
        connectWith: '.ul_comp_list',
        placeholder: 'placeholder'
    }).on('sortreceive', function (e, ui) {
        var element = ui.item;

        if (element.parent().parent().hasClass('comp_classify')) {
            element.find('div').addClass('btn-success');
        } else {
            element.find('div').removeClass('btn-success');
        }
    });

    $('#btn_save').click(function () {
        prepareForm();
        $('#process').val('1')
        $('#form_pane').submit();
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
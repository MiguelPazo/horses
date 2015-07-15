$(document).ready(function () {
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
});
$(document).ready(function () {
    $('#text_searched').focus();

    $('#btn_search').click(function (e) {
        e.preventDefault();
        search();
    });

    $('#text_searched').keypress(function (e) {
        if (e.which == 13) {
            search();
        }
    });

    var search = function () {
        var url = $('#btn_search').attr('href');
        var query = $('#text_searched').val();

        location.href = url + '?query=' + query;
    };
});
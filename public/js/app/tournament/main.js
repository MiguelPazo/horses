$(document).ready(function () {
    $('#btn_change_cat').click(function () {
        disableButtons(true);

        $.get(BASE_URL + 'tournament/categories_available', null, function (response) {
            disableButtons(false);

            if (response.success) {
                var html = '';

                $.each(response.data, function (i, item) {
                    var link = BASE_URL + 'tournament/change_category/' + item.id;
                    var button = '<a href="' + link + '" class="btn btn-primary">' +
                        item.description +
                        '</a>';
                    html += button;
                });
                $('#modal_change_category .modal-body').empty();
                $('#modal_change_category .modal-body').append(html);
                $('#modal_change_category').modal('show');
            } else {
                openPopup('Error', response.message, 1, null);
            }
        });
    });
});
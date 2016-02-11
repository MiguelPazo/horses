$(document).ready(function () {
    $('#btn_star').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');

        openPopup('Adventencia', 'Una vez se active la categoría no podrá desactivarse, ¿esta seguro?', 2, function () {
            $('#modal_confirm').modal('hide');
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.href = response.url;
                    } else {
                        openPopup('Error', response.message, 1, null);
                    }
                },
                error: function (response) {
                    generalError();
                }
            });
        });
    });
});
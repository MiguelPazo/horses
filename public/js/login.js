$(document).ready(function () {
    $('#formLogin').submit(function (e) {
        e.preventDefault();

        var filled = true;

        $(this).find('input').each(function (i, e) {
            var value = $(e).val().trim();

            if (value == null || value == '') {
                filled = false;
            }
        });

        if (filled) {
            var url = $(this).attr('action');
            var data = $(this).serialize();

            $.post(url, data, function (response) {
                if (response.success) {
                    location.href = response.url;
                } else {
                    alert(response.message)
                }
            });
        } else {
            alert('Debe llenar todos los campos.')
        }
    });
});
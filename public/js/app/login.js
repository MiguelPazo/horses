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

            $.ajax({
                url: url,
                method: 'POST',
                dataType: 'json',
                data: data,
                success: function (response) {
                    if (response.success) {
                        location.href = response.url;
                    } else {
                        openPopup('Error', response.message, 1, null);
                    }
                },
                error: function (response){
                    openPopup('Error', 'Ha ocurrido un error, se recargará la página', 1, null);

                    setTimeout(function(){
                        location.reload();
                    },2000);
                }
            });
        } else {
            openPopup('Error', 'Debe llenar todos los campos.', 1, null);
        }
    });
});
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
                        $('#modal_message').html(response.message);
                        $('#modal-container').modal( 'show' );
                    }
                },
                error: function (response){
                    $('#modal_message').html('Ha ocurrido un error, se recargará la página');
                    $('#modal-container').modal( 'show' );

                    setTimeout(function(){
                        location.reload();
                    },2000);
                }
            });
        } else {
            $('#modal_message').html('Debe llenar todos los campos.');
            $('#modal-container').modal( 'show' );
        }
    });
});